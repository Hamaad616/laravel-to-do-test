<?php

namespace App\Transformers;

use Illuminate\Http\JsonResponse;
use League\Fractal\TransformerAbstract;

class OtpVerificationTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        if($data instanceof JsonResponse){
            return $data->getData(true);
        }else{
            return $data;
        }
    }
}
