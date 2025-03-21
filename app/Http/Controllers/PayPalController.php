<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient; //srlive\paypalというライブラリーで支払いの作成、成功、キャンセルの処理を行う。
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Transaction;
use App\Models\Admin;

use Illuminate\Support\Facades\DB;

class PayPalController extends Controller
{
    public function payment(Request $request)
    {
        $price = $request->query('price');
        $totalPrice = $price + $price * 0.1;
        $id = $request->query('id');

        session(['project_id' => $id,'price' => $price, 'totalPrice' => $totalPrice]);

        $provider = new PayPalClient;//paypalClientをインスタンス化して、APIの認証情報をを設定
        $provider->setApiCredentials(config('paypal')); //config\paypalにapiの情報を設定しておく。
        $provider->getAccessToken();//この処理で、PayPal APIにアクセスするための「トークン」を取得. このトークンがないと、PayPalのAPIを使えない！

        $response = $provider->createOrder([
            "intent" => "CAPTURE", //支払いの意図。CAPTURE(即時決済),AUTHORIZE（保留決済)
            "application_context" => [//支払い後のリンク先を指定。
                "return_url" => route('company.paypal.success'),
                "cancel_url" => route('company.paypal.cancel'),
            ],
            "purchase_units" => [//支払いの通貨と金額を指定。
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $totalPrice
                    ]
                ]
            ]
            
            //             まとめ
            // ✅ createOrder() で 新しい支払いオーダーを作成 する。
            // ✅ intent: "CAPTURE" は 即時決済 を意味する。
            // ✅ return_url & cancel_url で 支払い成功・キャンセル後の遷移先 を設定。
            // ✅ purchase_units で 支払い金額や通貨を指定。
            // ✅ 成功すると 注文IDや支払いリンク がPayPalから返ってくる。
        ]);
    



        if (isset($response['id']) && $response['id'] != null) {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                    return redirect()->away($link['href']);//awayは外部のurlへのリダイレクト専用。to,routeは内部のurlへのリダイレクトのみに使用。
                }
            }
        }
//createOder()メソッドを実行すると、paypalのサーバーからリスポンスが変えてってきます。このリスポンスには、注文に関する複数のurlが含まれています。createOrder()のリスポンスの例は。。。。その中からapproveののリンクのリンクがあればリダイレクトする。


        return redirect()
            ->route('paypal.cancel')
            ->with('error', '支払い中に問題が発生しました。');
    }

    public function success(Request $request)
    {
        $project_id = session('project_id');
        $price = session('price');
        $totalPrice = session('totalPrice');

        $fee = $price * 0.1;
        $application = Application::where('project_id', $project_id)->first();
        $user = $application->freelancer->user->id;



       
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->query('token'));
        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            $paypalFee = $response['purchase_units'][0]['payments']['captures'][0]['seller_receivable_breakdown']['paypal_fee']['value'] ?? 0.00;

            Transaction::create([
                'payer_id' => auth()->id(),
                'payee_id' => 1,
                'project_id' => $project_id,
                'order_id' => $response['id'],
                'type' => 'escrow_deposit',
                'transaction_id' => $response['purchase_units'][0]['payments']['captures'][0]['id'] ?? null,
                'amount' => $price,
                'fee' => $fee,
                'currency' => $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'],
                'status' => 'COMPLETED',
            ]);

            Application::where('project_id',  session('project_id'))->update(['status' => 'ongoing']);


            Admin::where('user_id','1')->update([
                'balance' => DB::raw('balance + ' . $totalPrice),
                'total_fee_revenue' => DB::raw('total_fee_revenue + ' . $fee),
                'escrow_balance' => DB::raw('escrow_balance + ' . $price),
            ]);
              

            
            return redirect()
                ->route('company.project.on_going')
                ->with('success', '支払いが完了しました。');
        }

        return redirect()
            ->route('home')
            ->with('error', '支払いの処理中に問題が発生しました。');
    }

    public function cancel()
    {
        return redirect()
            ->route('home')
            ->with('error', '支払いがキャンセルされました。');
    }

    

}


//このコントローラーでは支払いのプロセスを開始し、ユーザーのpaypalの支払いのページにリダイレクトする

//apiとは---
//api(application programming interface)で異なるシステムやアプリケーションが通信するための仕組み。apiを介してやりとりを行う。今回はlarabelからpaylapで支払いを作成してしてとリクエストを送る。paypalは,ok,支払いリンクを送ったよとリスポンスをしている。つまり、外部のサービスをやりとりをする時に、apiを通して行う。tokenが必要である。
//-----------



//token とは
// ✅ トークンは、APIを安全に利用するための「一時的な鍵」
// ✅ トークンがないと、誰でもAPIを使えてしまい、セキュリティリスクが高まる
// ✅ アクセストークンは短期間有効で、リフレッシュトークンを使って更新する
// ✅ セキュリティ対策をしっかり行うことで、安全にAPIを運用できる
//2️⃣ トークンの流れ（例：PayPal API）
// トークンの発行
// クライアント（アプリ）が APIサーバーに認証情報を送信
// サーバーが認証に成功すると、アクセストークンが発行 される
// ② トークンを使ってAPIリクエスト
// 取得したトークンをAPIリクエストのヘッダーに付与 して送る
// APIサーバーがトークンをチェック し、正しければデータを返す
// ③ トークンの有効期限が切れたら再発行
// アクセストークンには有効期限（例：1時間） がある
// 有効期限が切れたら、リフレッシュトークンを使って新しいトークンを取得


//---


//createOrder()のリスポンスの例
// {
//     "id": "5O190127TN364715T",
//     "status": "CREATED",
//     "links": [
//       {
//         "href": "https://api.paypal.com/v2/checkout/orders/5O190127TN364715T",
//         "rel": "self",
//         "method": "GET"
//       },
//       {
//         "href": "https://www.paypal.com/checkoutnow?token=5O190127TN364715T",
//         "rel": "approve",
//         "method": "GET"
//       },
//       {
//         "href": "https://api.paypal.com/v2/checkout/orders/5O190127TN364715T",
//         "rel": "update",
//         "method": "PATCH"
//       },
//       {
//         "href": "https://api.paypal.com/v2/checkout/orders/5O190127TN364715T/capture",
//         "rel": "capture",
//         "method": "POST"
//       }
//     ]
//   }
  


