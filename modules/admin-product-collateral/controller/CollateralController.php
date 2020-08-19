<?php
/**
 * CollateralController
 * @package admin-product-collateral
 * @version 0.0.1
 */

namespace AdminProductCollateral\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibForm\Library\Combiner;
use LibPagination\Library\Paginator;
use ProductCollateral\Model\{
    ProductCollateral as PCollateral,
    ProductCollateralChain as PCChain
};

class CollateralController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['product', 'collateral']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product_collateral)
            return $this->show404();

        $collateral = (object)[];

        $id = $this->req->param->id;
        if($id){
            $collateral = PCollateral::getOne(['id'=>$id]);
            if(!$collateral)
                return $this->show404();
            $params = $this->getParams('Edit Product Collateral');
        }else{
            $params = $this->getParams('Create New Product Collateral');
        }

        $form           = new Form('admin.product-collateral.edit');
        $params['form'] = $form;


        if(!($valid = $form->validate($collateral)) || !$form->csrfTest('noob'))
            return $this->resp('product/collateral/edit', $params);
        
        if($id){
            if(!PCollateral::set((array)$valid, ['id'=>$id]))
                deb(PCollateral::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!PCollateral::create((array)$valid))
                deb(PCollateral::lastError());
        }

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => $id ? 2 : 1,
            'type'   => 'product-collateral',
            'original' => $collateral,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminProductCollateral');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product_collateral)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $collaterals = PCollateral::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($collaterals)
            $collaterals = Formatter::formatMany('product-collateral', $collaterals, ['user']);
        
        $params                = $this->getParams('Product Collateral');
        $params['collaterals'] = $collaterals;
        $params['form']        = new Form('admin.product-collateral.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = PCollateral::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminProductCollateral'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('product/collateral/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product_collateral)
            return $this->show404();

        $id         = $this->req->param->id;
        $collateral = PCollateral::getOne(['id'=>$id]);
        $next       = $this->router->to('adminProductCollateral');
        $form       = new Form('admin.product-collateral.index');

        if(!$collateral)
            return $this->show404();

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'product-collateral',
            'original' => $collateral,
            'changes'  => null
        ]);

        PCollateral::remove(['id'=>$id]);
        PCChain::remove(['collateral'=>$id]);
        
        $this->res->redirect($next);
    }
}