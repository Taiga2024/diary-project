<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\{Hash,Auth};
use Illuminate\Validation\ValidationException;
use Nuwabe\LightHouse\Execution\ResolveInfo;
use Nuwabe\LightHouse\Support\Contracts\GraphQLContext;

final readonly class AuthResolver
{
    /** @param  array{}  $args */
    public function register(mixed $root, array $args)
    {
        ["name"=>$name,"email"=>$email,"password"=>$password]=$args;
        
        $user=User::create([
            "name" => $name,
            "email" => $email,
            "password" => Hash::make($password),
        ]);

        $token=$user->createToken($email)->plainTextToken;
        
        return [
            "user"=>$user,
            "token"=>$token,
        ];
    }

    public function login(mixed $root, array $args)
    {
        ["email"=>$email,"password"=>$password]=$args;

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw new ValidationException([
                "email" => ["このユーザーは存在しません"]
            ]);
        }

        $token = $user->createToken($email)->plainTextToken;

        
        return [
            "user"=>$user,
            "token"=>$token,
        ];
    }

    public function logout(mixed $root, array $args)
    {
        $authUser=Auth::guard("sanctum")->user();

        if (!$authUser) {
            throw new \Exception("このユーザーは認証されていません");
        }
        
        $authUser->tokens()->where('tokenable_id', $authUser->id)->delete();

        return [
            "success" => true,
            "user" =>$authUser,
        ];
    }
}
