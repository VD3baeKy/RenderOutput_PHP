# PHPUnit
## UpdateFunctionsTest
* DBとのやりとり（SELECT・UPDATE）が正常に行われるかどうか」が主目的。
* updateProduct(), getProductById(), getAllVendorCodes() の３つの関数にPHPコードを分割し、その関数についてPHPUnitテストを実施。
    - ⇒ そのため、外部依存(DB・POST/P​OST/_GET)をモックにして、関数に分離するとテストしやすくなる。
        - 本番コードを直接テストするのではなく、テストしやすい形に分離する。
        - PDO や PDOStatement はモック化（ PHPUnit の createMock() ）してテスト。
        - updateProduct関数, getProductById関数, getAllVendorCodes関数の単位でユニットテスト。
        - 実際にDBへのアクセスは行わず、あくまでもSQLクエリを正常実行できるかを担保するためのテスト。
