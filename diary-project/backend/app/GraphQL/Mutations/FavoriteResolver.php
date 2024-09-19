<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Nuwabe\LightHouse\Execution\ResolveInfo;
use Nuwabe\LightHouse\Support\Contracts\GraphQLContext;

final readonly class FavoriteResolver
{
    /** @param  array{}  $args */
    public function store(null $_, array $args)
    {
        ["diary_id"=>$diary_id]=$args;
        $authUser=Auth::guard("sanctum")->user();

        $isFavorite=Favorite::where('user_id',$authUser->id)->where('diary_id',$diary_id)->exists();

        

        if (!$isFavorite) {
            $favorite=Favorite::create([
                "diary_id"=>$diary_id,
                "user_id" => $authUser->id,
            ]);
            return [
                "status" => true,
            ];
        }

        return [
            "status" => false,
        ];        
    }

    public function delete(null $_, array $args){
        ["diary_id"=>$diary_id,"favorite_id"=>$favorite_id]=$args;
        $authUser=Auth::guard("sanctum")->user();


        $isFavorite=Favorite::where('user_id',$authUser->id)->where('diary_id',$diary_id)->exists();

        if (!$isFavorite) {
            Favorite::where("id", $favorite_id)->delete();
            return [
                "status" => true,
            ];
        }

        return [
            "status" => false,
        ]; 
    }
}
