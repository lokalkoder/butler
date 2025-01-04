<?php

namespace Lokal\Butler\Repositories;

use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

class CommonPresenter extends FractalPresenter
{
    /**
     * @throws \Exception
     */
    public function __construct(protected TransformerAbstract $transformer)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function getTransformer(): TransformerAbstract
    {
        return $this->transformer;
    }
}