# 学んだこと

## 環境構築
- 環境構築ではDockerを使い、Backendではphp-fpmコンテナとnginxコンテナを使って構築して、FrontendではNuxt.jsとnginxコンテナを使って構築した。

- composerはマルチステージビルドでインストールすることでイメージのサイズを削減できる。

- 特にstorageなど権限の問題が多く発生したのでchmodコマンドやchownコマンドで権限を適切に与えることが大切
  

### chmodとchownの違い

<table>
  <thead>
    <tr>
      <th>コマンド</th> <th>説明</th>
    </tr>
  </thead>
  <tr>
    <td> chmod </td> <td>ファイルやディレクトリの<strong>パーミッション(権限)</strong>を変更 (今回はこっちを使った)</td>
  </tr>
  <tr>
    <td> chown </td> <td>ファイルやディレクトリの所有者を変更する</td>
  </tr>
</table>


### マルチステージビルドとは
- イメージが肥大化しないように必要な部分だけ他のイメージから取得する

``` docker
COPY --from=composer /usr/bin/composer /usr/bin/composer
```
このようにphpコンテナのdockerfileに記述することでcomposerのイメージの中から/usr/bin/composerだけをコピーできる。このようにすることで、composerをインストールするためにインストーラーをダウンロードするなどしないでcomposerのみをダウンロードできる。

### 参考

https://qiita.com/t-a-run/items/239ed690ece7a011804a#chmod%E3%81%AE%E4%BD%BF%E3%81%84%E6%96%B9

https://zenn.dev/chamo/articles/fe98c8c8e65138

https://rnakamine.hatenablog.com/entry/2020/09/17/232707


## フロントエンド

### Vue.js
- vueはv-ifなどのディレクティブをHTMLベースのテンプレート構文のなかで使うことで様々な表示ができる。
- リアクティブな値を作成する場合はrefを使う。
- データの受け渡しはpropsを使う
- vueのデータの流れは一方向。これはデータの流れを管理しやすくするため。
- クリックイベントを子コンポーネントに伝えたい場合はv-model、emit、propsをうまく使う必要がある。

### Nuxt.js
- Nuxt.jsではpages配下のフォルダでファイルベースルーティングが行われている。
- ダイナミックルーティングを行う場合は **フォルダ名/[id].vue** のようにする。この際。フォルダ配下にindex.vueを作るとフォルダ名/のページが作成される。
- utilsフォルダの配下に作った関数はオートインポートされる。
- envファイルを使いたい場合はprocess.envを使う。

### envファイルを使う方法。
1. envファイルにNUXT_という接頭辞をつけた環境変数を作る

``` typescript
NUXT_PUBLIC_API_URL=http://
```

2. nuxt.config.tsに以下のように記述する

``` typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiUrl: '',
    }
  },
})
```
3. useRuntimeConfig()を使うことで必要な時に値を取得できる。

``` typescript
const runtimeConfig = useRuntimeConfig();
const apiUrl: string = runtimeConfig.public.apiUrl as string;
```

### graphql-request
- フロントエンドからバックエンドのGraphQLサーバにリクエストを行うためのライブラリ。
- query, variables, headersをフロントエンドで定義してバックエンドにリクエストを行い、欲しいデータをフェッチする。

### TypeScriptのRecord<Keys, Type>
Record<Keys, Type>はプロパティのキーがKeysであり、プロパティの値がTypeであるオブジェクトの型を作るユーティリティ型です。

``` typescript
import { GraphQLClient } from "graphql-request";


interface RequestOptions<V = Record<string, unknown>> {
  query: string;
  variables?: V;
  headers?: Record<string, string>;
}

export default async function gqlRequest<T=unknown,V = Record<string, unknown>>({
  query,
  variables,
  headers,
}: RequestOptions<V>):Promise<T> {

  const runtimeConfig = useRuntimeConfig();
  const apiUrl: string = runtimeConfig.public.apiUrl as string;
  const client = new GraphQLClient(apiUrl);
  if (headers) {
    client.setHeaders(headers);
  }
  
  try {
    if (variables) {
      const data = await client.request<T>(query, variables);
      return data;
    } else {
      const data = await client.request<T>(query);
      return data;
    }
  } catch (error) {
    console.error(error)
    throw error;
  }
}
```
入れ子構造になっているvariablesやheadersを簡潔に書くために使えました。また、GenericsにRecordを使うことでbackendへのRequestに汎用的に使える関数になりました。

Recordを使わない場合
``` typescript
import { GraphQLClient } from "graphql-request";

interface valiablesType{
  "input": unknown;
}

interface headerType{
  "Authorization": string;
}

interface RequestOptions<V=valiablesType> {
  query: string;
  variables?: V;
  headers?: headerType;
}
// 省略
```

### 参考

https://www.oio-blog.com/contents/vue3

https://tyotto-good.com/nuxtjs/environment-variable#.env%20%E3%81%A8%20runtimeConfig%20%E3%82%92%E7%94%A8%E3%81%84%E3%81%9F%E7%92%B0%E5%A2%83%E5%A4%89%E6%95%B0%E3%81%AE%E5%88%A9%E7%94%A8%E6%96%B9%E6%B3%95%E3%80%90Nuxt3%E3%80%91

https://qiita.com/kenogi/items/7c28d237da70f83ed60e

https://typescriptbook.jp/reference/type-reuse/utility-types/record

## バックエンド

### Laravel

1. 外部キー制約は
``` php
foreignId('user_id')->constrained()->cascadeOnDelete();
```
のようにすることで簡単に行える。


2. 明示的に行いたい場合は引数にテーブルを指定する。
``` php
$table->foreignId('following_id')->constrained('users')->cascadeOnDelete()->comment('フォローしているユーザーID');
$table->foreignId('followed_id')->constrained('users')->cascadeOnDelete()->comment('フォローされているユーザーID');
```
### GraphQL

<table>
  <caption>基本概念</caption>
  <thead>
    <tr>
      <th>クエリの種類</th> <th>説明</th> <th>RESTでの効果</th>
    </tr>
  </thead>
  <tr>
    <td> query </td> <td>データの取得</td> <td>GET</td>
  </tr>
  <tr>
    <td> mutation </td> <td>データの更新</td> <td>POST/PUT/DELETE...</td>
  </tr>
  <tr>
    <td> subscription </td> <td>イベントの検知</td> <td>Websocket</td>
  </tr>
</table>

GraphQLではRESTAPIとは違い一つのエンドポイント` /graphql `のみでデータの取得や更新などを行うことができる。バックエンド側でスキーマを定義して、フロントエンド側で必要なデータのqueryを定義することによって必要なデータのみに効率的にアクセスできる。
またフロントエンドとバックエンドが分離しているのでフロントエンドの変更に強い。

スキーマ
``` graphql
extend type Query {
    "Find a single user by an identifying attribute."
    user(
      "Search by primary key."
      id: ID @eq @rules(apply: ["prohibits:email", "required_without:email"])

      "Search by email address."
      email: String @eq @rules(apply: ["prohibits:id", "required_without:id", "email"])
    ): User @find

    "List multiple users."
    users(
      "Filters by name. Accepts SQL LIKE wildcards `%` and `_`."
      name: String @where(operator: "like")
    ): [User!]! @paginate(defaultCount: 10)

     me: User! @auth(guards: ["sanctum"])
}

type User {
    id: ID!
    profile_image: String
    diaries: [Diary!]! @hasMany(defaultCount: 10,type: PAGINATOR)
    favorites: [Favorite!]! @hasMany(relation: "favorites")
    name: String!
    email: String!
    email_verified_at: DateTime
    created_at: DateTime!
    updated_at: DateTime!
}
```
クエリ
```graphql
{
  user(id:1){
    id
    name
  }
}

```
### Laravel LightHouse
Laravelの機能をgraphqlで使えるライブラリ。queryだけで処理できない複雑な処理はリゾルバ関数を使う。

クエリ
```graphql
extend type Mutation {
  register(input:RegisterInput! @spread):AuthPayload @field(resolver:"App\\GraphQL\\Mutations\\AuthResolver@register")
  login(input:LoginInput! @spread):AuthPayload @field(resolver:"App\\GraphQL\\Mutations\\AuthResolver@login")
  logout:Logout! @field(resolver:"App\\GraphQL\\Mutations\\AuthResolver@logout")
}
```
リゾルバ関数
```php
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
            throw new \Exception("このユーザーは存在しません");
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
            "status" => true,
            "user" =>$authUser,
        ];
    }
}

```
### 参考

https://readouble.com/laravel/12.x/ja/migrations.html

https://qiita.com/shotashimura/items/3f9e04b93e79592030a4

https://qiita.com/ucan-lab/items/e7c151133541fbeaebe9

## AWS

### VPC
仮想のネットワークを作成するサービス。イメージは国。
CIDR表記というもので、ネットワークの範囲を決める。

### サブネット
VPCを細かく区切ったもの。イメージは都道府県。
Internet Gatewayとつながっているものをpublic subnet、つながっていないものをprivate subnetという。

### Internet Gateway
vpcをインターネット空間と接続するもの。イメージは港。

### Route table
ネットワークの行先を定義するもの。イメージは道路標識
ルートテーブルでInternet Gatewayとサブネットを紐づけることでネットワークの行先を管理できる。

### Security group
Security groupはAWSのファイアウォールのようなもの。不正アクセスからネットワークを守るために通していい通信とそれ以外を定義する。イメージは門番
インバウンドルールは入ってくる通信に対するルール。
アウトバウンドルールは出で行くものを制限するルール。

### ALB
負荷分散を行うために通信を適切に分散させるもの。サーバの冗長化や負荷分散で使われる。イメージは複数車線の道路入り口。

### ターゲットグループ
ロードバランサーを行うターゲットをまとめたもの。イメージはビル群。

### ECR
ECSで構築するdockerimageのリポジトリのようなもの。
### ECS fargate
AWS上でコンテナアプリケーションを構築できるもの。

<table>
  <caption>構成要素</caption>
  <thead>
    <tr>
      <th>要素</th> <th>説明</th>
    </tr>
  </thead>
  <tr>
    <td> タスク定義 </td> <td>タスクを作成するためのテンプレート</td>
  </tr>
  <tr>
    <td> タスク </td> <td>アプリケーションを実行する実行単位。イメージはdocker-compose.yml</td>
  </tr>
  <tr>
    <td> サービス </td> <td>タスクの管理。LBの設定やタスクの数などを管理</td>
  </tr>
  <tr>
    <td>クラスター </td> <td>サービスを管理するもの</td>
  </tr>
</table>   

### Secret Manager
データベースの情報などenvファイルに書くような情報をAWS上で安全に扱うためのもの。

### RDS
AWSでのDB。マルチAZ機能があり冗長化や負荷分散ができる。

### 参考

https://zoo200.net/aws-tutorial-alb-ec2-1/#toc20

https://baresupport.jp/blog/2023/09/05/223/

https://qiita.com/K5K/items/0d8dbdb39fbb0375e2bd

https://techblog.ap-com.co.jp/entry/2024/06/14/093000