# PHPUnit テスト概要 (RenderOutput_PHP)
* 「DBとのやりとり（SELECT・UPDATE）が正常に行われるかどうか」が主目的。

---

## common_functions.php
* 共通の関数はcommon_functionsとしてまとめる。
    - getAllVendorCodes()
    - getPDO()

---

## UpdateFunctionsTest
* updateProduct(), getProductById(), getAllVendorCodes() の３つの関数にPHPコードを分割し、その関数についてPHPUnitテストを実施。
    - ⇒ そのため、外部依存(DB・POST/P​OST/_GET)をモックにして、関数に分離するとテストしやすくなる。
        - 本番コードを直接テストするのではなく、テストしやすい形に分離する。
        - PDO や PDOStatement はモック化（ PHPUnit の createMock() ）してテスト。
        - updateProduct関数, getProductById関数, getAllVendorCodes関数の単位でユニットテスト。
        - 実際にDBへのアクセスは行わず、あくまでもSQLクエリを正常実行できるかを担保するためのテスト。

---

## CreateFunctionsTest
* createProduct(), getAllVendorCodes(）の２つの関数にPHPコードを分割し、その関数についてPHPUnitテストを実施。
    - createProduct() : productsテーブルへのINSERT部分のテスト。
    - getAllVendorCodes() : vendorsテーブルからvendor_codeリスト取得のテスト。
    - ロジック単体テストなので、副作用や外部依存のテストはモック化して実行。

---

## DeleteFunctionsTest
* deleteProduct関数は「id指定のレコード削除」を担当している。
* PDOとPDOStatementをモック化し、「SQL実行」,「rowCount」の呼び出しを検証。
* 削除件数（rowCount()の返値）が想定通りかを確認。

---

## ReadFunctionsTest
* getProducts関数 … 検索キーワードと並び順で取得する処理を抽象化。
* PDOとPDOStatementをモック化し、SQLおよび、引数の通りにprepare/execute/fetchAllが正しく呼ばれることを検証。
* ソートは、昇順/降順/デフォルト の3ケースを検証。


