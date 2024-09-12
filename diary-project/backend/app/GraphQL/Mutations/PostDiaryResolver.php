<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Diary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Gemini\Laravel\Facades\Gemini;
use Nuwabe\LightHouse\Execution\ResolveInfo;
use Nuwabe\LightHouse\Support\Contracts\GraphQLContext;

final readonly class PostDiaryResolver
{
    /** @param  array{}  $args */
    public function post(null $_, array $args)
    {
        ["title"=>$title,"text"=>$text]=$args;
        $authUser=Auth::guard("sanctum")->user();

        if (!$authUser) {
            throw new \Exception("このユーザーは認証されていません");
        }
        
        $toGeminiCommand = "# やって欲しいこと\n".$title."\n".$text."\n"."上記の分を添削して以下の形式で返してください"."\n"."添削した文章\n" ."添削した文章の日本語訳\n"."添削した文章の重要英単語\n"."添削した文章で使われている英文法解説\n";

        $result = Str::markdown(Gemini::geminiPro()->generateContent($toGeminiCommand)->text());
        
        $diary=Diary::create([
            "title" => $title,
            "text" => $result,
            "user_id" => $authUser->id,
        ]);

        return $diary;
    }

    public function update(null $_, array $args)
    {
        ["id"=>$id,"title"=>$title,"text"=>$text]=$args;
        $authUser=Auth::guard("sanctum")->user();
        $updateDiary=Diary::where('id', $id)->first();

        if (!$authUser) {
            throw new \Exception("このユーザーは認証されていません");
        }

        if ($authUser->id!=$updateDiary->user->id) {
            throw new \Exception("この日記の作者が違います");
        }
        
        $toGeminiCommand = "# やって欲しいこと\n".$title."\n".$text."\n"."上記の分を添削して以下の形式で返してください"."\n"."添削した文章\n" ."添削した文章の日本語訳\n"."添削した文章の重要英単語\n"."添削した文章で使われている英文法解説\n";

        $geminiResponse = Str::markdown(Gemini::geminiPro()->generateContent($toGeminiCommand)->text());

        $diary=$updateDiary->update([
             "title" => $title,
             "text" => $geminiResponse,
        ]);

        $result=Diary::where('id', $id)->first();
        
        return $result;
    }
}
