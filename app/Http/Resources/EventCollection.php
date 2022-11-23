<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;

class EventCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    private function item($value){
        return [
            'id' => $value->id,
            'title' => $value->title,
            'description' => $value->description,
            'day' => $value->day,
            'month' => $value->month,
            'year' => $value->year,
            'hour' => $value->hour,
            'minute' => $value->minute,
            'created_at' => $value->created_at,
            'uuid' => $value->UUID,
            'config' => $value->config,
            'users' => [
                [
                    'id' => $value->user_id,
                    'name' => $value->name,
                    'avatar' => $value->avatar
                ]
            ]
        ];
    } 
    public function toArray($request)
    {
        $collection = collect([]);
        foreach ($this->collection as $key => $value) {
            $find = $collection->search(function($item, $key) use ($value){
                return $item["id"] == $value->id;
            });
            $data = $this->item($value);
            if(gettype($find) == "boolean"){
                $collection->push($data);
            } else {
                $pull = $collection->pull($find);
                array_push($pull["users"], $data["users"][0]);
                $collection->push($pull);
            }
        }
        return $collection;
    }
}
