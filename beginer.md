PHẦN 1: XÂY DỰNG ỨNG DỤNG LARAVEL WEB HOÀN CHỈNH 

Bài : THIẾT LẬP MÔI TRƯỜNG VÀ KHỞI TẠO 

1.1 Kiểm tra công cụ 

Trước khi bắt đầu, hãy đảm bảo máy tính đã có: 

PHP (>= 8.2): Ngôn ngữ chính. 

Composer: Trình quản lý thư viện. 

Database: MySQL hoặc PostgreSQL (Có thể dùng Laragon/XAMPP để có sẵn). 

1.2 Khởi tạo Project 

Mở Terminal và chạy lệnh: 

composer create-project laravel/laravel my-blog cd my-blog 

1.3 Cấu hình quan trọng ban đầu 

File .env: Mở file và cấu hình Database (DB_DATABASE, DB_USERNAME, DB_PASSWORD). 

Tạo App Key: php artisan key:generate 

 

 

 

 

 

 

 

 

 

 

 

 

Bài : KIẾN TRÚC MVC VÀ LUỒNG XỬ LÝ WEB 

Trong Laravel Web truyền thống, luồng dữ liệu đi theo sơ đồ: 
Browser -> Route -> Controller -> Model (Database) -> Controller -> View (Blade) -> Browser (HTML) 

2.1 Tạo Model và Migration cho Bài viết (Post) 

Chúng ta sẽ xây dựng chức năng Blog.  

Chạy lệnh: php artisan make:model Post -m 

Lệnh này tạo ra file Model app/Models/Post.php và file Migration trong database/migrations. 

Cấu trúc bảng posts: Mở file migration vừa tạo và thêm: 

Schema::create('posts', function (Blueprint $table) {  

$table->id(); $table->string('title'); 

$table->text('content'); $table->timestamps(); 

}); 

Sau đó chạy: php artisan migrate để tạo bảng vào database. 

2.2 Khai báo Route (Tuyến đường) 

Mở routes/web.php. Đây là nơi định nghĩa các URL cho người dùng: 

use App\Http\Controllers\PostController; use Illuminate\Support\Facades\Route; Route::get('/posts', [PostController::class, 'index'])->name('posts.index'); Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create'); Route::post('/posts', [PostController::class, 'store'])->name('posts.store'); 

Bài : CONTROLLER VÀ LOGIC NGHIỆP VỤ 

Tạo Controller để điều khiển: php artisan make:controller PostController 

3.1 Hiển thị danh sách (Hàm index) 

public function index() {  

$posts = Post::latest()->get(); // Lấy tất cả bài viết từ database 

return view('posts.index', compact('posts')); // Trả về file giao diện Blade 

 } 

3.2 Lưu dữ liệu từ Form (Hàm store) 

public function store(Request $request) {  

// 1. Validation (Kiểm tra dữ liệu đầu vào) 

$data = $request->validate([ 'title' => 'required|max:255', 'content' => 'required', ]);  

// 2. Lưu vào Database  

Post::create($data);  

// 3. Chuyển hướng kèm thông báo  

return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo!'); 

 } 

Bài : VIEW (BLADE TEMPLATE) - GIAO DIỆN NGƯỜI DÙNG 

Laravel sử dụng Blade để render HTML. Các file nằm trong resources/views. 

4.1 Hiển thị danh sách (posts/index.blade.php) 

<h1>Danh sách bài viết</h1>  

@if(session('success')) 

 <p style="color: green">{{ session('success') }}</p> 

@endif  

@foreach($posts as $post)  

<h3>{{ $post->title }}</h3>  

<p>{{ $post->content }}</p>  

<hr>  

@endforeach 

4.2 Form tạo bài viết (posts/create.blade.php) 

<form action="{{ route('posts.store') }}" method="POST">  

@csrf <!-- Bắt buộc để bảo mật Web trong Laravel -->  

<input type="text" name="title" placeholder="Tiêu đề">  

<textarea name="content" placeholder="Nội dung"></textarea>  

<button type="submit">Lưu bài viết</button>  

</form> 

Dev note: @csrf là điểm khác biệt lớn nhất giữa Web và API. Web cần Token này để chống tấn công giả mạo, trong khi API sẽ dùng phương thức khác. 

Hình dạng 

Câu hỏi ôn tập: 

Tại sao cần chạy php artisan migrate? 

Mục đích của @csrf trong Form là gì? 

Sự khác biệt giữa routes/web.php và logic trả về giao diện? 

 

 

Phần 2 : BƯỚC ĐỆM CHUYỂN ĐỔI (THE BRIDGE) 

TỪ GIAO DIỆN (HTML) SANG DỮ LIỆU THÔ (JSON) 

Sau khi đã hoàn thành ứng dụng Web ở Phần 1, bạn đã thấy cách Laravel trả về các trang HTML đẹp mắt thông qua Blade. Tuy nhiên, nếu bạn muốn xây dựng một ứng dụng di động (iOS/Android) hoặc một trang web hiện đại (React/Vue), bạn không thể dùng lại các file Blade đó. Đây là lúc bạn cần API. 

1. Sự khác biệt cốt lõi: Web vs. API 

Hãy tưởng tượng ứng dụng của bạn là một nhà hàng: 

Laravel Web (Monolith): Giống như thực khách đến ăn tại chỗ. Nhà hàng chuẩn bị đồ ăn, bày lên đĩa (Blade), trang trí đẹp mắt và phục vụ tận bàn (HTML). 

Laravel API: Giống như dịch vụ giao đồ ăn. Nhà hàng chỉ chuẩn bị nguyên liệu cốt lõi (Dữ liệu), đóng gói vào hộp nhựa tiêu chuẩn (JSON) và gửi đi. Thực khách (Frontend/Mobile) tự mở hộp và trình bày theo ý họ. 

Bảng so sánh kỹ thuật: 

Đặc điểm 

Laravel Web 

Laravel API 

Đầu ra (Output) 

HTML/CSS (Giao diện hoàn chỉnh) 

JSON (Dữ liệu thô) 

Tuyến đường (Route) 

routes/web.php 

routes/api.php 

Tiền tố URL 

ten-mien.com/posts 

ten-mien.com/api/posts 

Xác thực 

Session & Cookie (Stateful) 

Token - Sanctum (Stateless) 

Bảo mật 

Chống CSRF qua @csrf 

Không dùng CSRF (dùng Token) 

Công cụ thử nghiệm 

Trình duyệt (Chrome, Edge...) 

Postman, Insomnia, cURL 

 

2. Thay đổi tư duy trong Controller 

Điểm khác biệt lớn nhất nằm ở cách Controller "trả lời" yêu cầu từ Client. 

Ví dụ: Hàm lưu bài viết (Store) 

Trong Laravel Web: 

public function store(Request $request) {  

$data = $request->validate([...]); Post::create($data); // Web: Chuyển hướng người dùng về trang danh sách  

return redirect()->route('posts.index')->with('message', 'Thành công!');  

} 

Trong Laravel API: 

public function store(Request $request) {  

$data = $request->validate([...]);  

$post = Post::create($data); // API: Trả về dữ liệu bài viết vừa tạo kèm mã trạng thái 201 (Created)  

return response()->json([ 'status' => 'success', 'data' => $post ], 201);  

} 

3. Hiểu về tính chất "Stateless" (Không lưu trạng thái) 

Web truyền thống: Trình duyệt lưu Session ID trong Cookie. Server dựa vào đó để biết "Bạn là ai" mà không cần bạn gửi mật khẩu lại mỗi lần bấm nút. 

API chuyên nghiệp: Mỗi request gửi lên Server là độc lập. Server không "nhớ" bạn là ai từ lần trước. Do đó, mỗi lần gửi yêu cầu, bạn phải đính kèm một chiếc "thẻ thông hành" (được gọi là Bearer Token) trong phần Header của yêu cầu. 

4. Tại sao API không cần CSRF? 

Trong phần Web, bạn bắt buộc phải có @csrf trong form để ngăn chặn kẻ xấu giả mạo yêu cầu từ trang web khác. 
Tuy nhiên, với API: 

Chúng ta không dùng Session/Cookie để xác thực. 

Kẻ xấu không thể lấy trộm Token của người dùng dễ dàng như Cookie. 

Do đó, hệ thống API của Laravel (trong routes/api.php) mặc định đã tắt lớp bảo mật CSRF để các ứng dụng bên ngoài (Mobile, React) có thể kết nối dễ dàng. 

5. Công cụ mới cho kỷ nguyên mới: Postman 

Trình duyệt chỉ có thể hiển thị tốt các trang HTML (phương thức GET). Để làm việc với API (POST, PUT, DELETE), bạn không thể dùng trình duyệt thông thường. 

Postman (hoặc Insomnia) sẽ trở thành "trình duyệt mới" của bạn. 

Tại đây, bạn sẽ học cách nhìn vào các mã trạng thái (Status Code): 

200: Mọi thứ đều ổn. 

201: Đã tạo mới thành công. 

401: Bạn chưa đăng nhập. 

422: Dữ liệu bạn gửi lên bị lỗi (sai định dạng). 

500: Lỗi hệ thống Backend. 

 

 

 

 

 

PHẦN 3: XÂY DỰNG BACKEND API CHUYÊN NGHIỆP 

Bài : THIẾT LẬP HỆ THỐNG API (LARAVEL 11) 

Trong các phiên bản Laravel mới nhất (từ v11), hệ thống API không được kích hoạt sẵn để giữ cho ứng dụng nhẹ nhất có thể. 

1.1 Kích hoạt API và Sanctum 

Mở Terminal và chạy lệnh: php artisan install:api 

Lệnh này sẽ tự động thực hiện 3 việc: 

Tạo file routes/api.php để định nghĩa các đường dẫn API. 

Cài đặt Laravel Sanctum (thư viện tiêu chuẩn để quản lý Token). 

Tạo bảng personal_access_tokens trong Database để lưu trữ mã đăng nhập. 

1.2 Cấu hình Route API 

Mở routes/api.php. Điểm khác biệt lớn nhất là các Route ở đây sẽ tự động có tiền tố (prefix) là /api/. 

use App\Http\Controllers\Api\PostController;  

use Illuminate\Support\Facades\Route; // Sử dụng apiResource để tạo nhanh 5 route chuẩn CRUD (Index, Store, Show, Update, Destroy)  

Route::apiResource('posts', PostController::class); 

URL thực tế sẽ là: http://ten-mien.com/api/posts 

CHƯƠNG 2: TẠO API CONTROLLER VÀ RESOURCE 

2.1 Tạo API Controller 

Chúng ta nên tách biệt API Controller khỏi Web Controller để dễ quản lý. 

php artisan make:controller Api/PostController –api 

Flag --api sẽ loại bỏ các hàm không cần thiết cho API như create() và edit() (vốn dùng để hiển thị form). 

2.2 API Resource - Lớp chuyển đổi dữ liệu (Quan trọng) 

Đây là "vũ khí bí mật" của Laravel. Thay vì trả về toàn bộ dữ liệu thô từ Database, ta dùng Resource để lọc và định dạng lại dữ liệu. 

php artisan make:resource PostResource 

Mở app/Http/Resources/PostResource.php và chỉnh sửa hàm toArray: 

public function toArray($request): array {  

return [ 'id' => $this->id, 'title' => $this->title, 'content' => $this->content, 'created_at' => $this->created_at->format('d-m-Y'), // Định dạng lại ngày tháng ]; 

 } 

Bài : VIẾT LOGIC CRUD CHO API 

Mở app/Http/Controllers/Api/PostController.php và hoàn thiện các hàm: 

3.1 Lấy danh sách (Index) 

public function index() {  

$posts = Post::latest()->paginate(10); // Phân trang cho API return PostResource::collection($posts); // Trả về danh sách đã qua bộ lọc Resource  

} 

3.2 Tạo mới bài viết (Store) 

public function store(Request $request) {  

$data = $request->validate([ 'title' => 'required|max:255', 'content' => 'required', ]); $post = Post::create($data); return (new PostResource($post)) ->response() ->setStatusCode(201); // Trả về mã 201: Đã tạo thành công  

} 

3.3 Hiển thị chi tiết (Show) 

public function show(Post $post) { return new PostResource($post); } 

public function destroy(Post $post) {  

$post->delete();  

return response()->json(['message' => 'Deleted successfully'], 204); // 204: No Content  

} 

Bài : XÁC THỰC VÀ BẢO MẬT VỚI SANCTUM 

Để bảo vệ API, không cho người lạ tùy ý xóa/sửa bài viết, chúng ta sử dụng Middleware của Sanctum. 

4.1 Bảo vệ Route 

Quay lại file routes/api.php, bao bọc các route quan trọng: 

Route::middleware('auth:sanctum')->group(function () {  

Route::post('/posts', [PostController::class, 'store']);  

Route::delete('/posts/{post}', [PostController::class, 'destroy']);  

}); 

4.2 Cách thức hoạt động của Token 

Login: Người dùng gửi email/password tới Server. 

Cấp Token: Server kiểm tra, nếu đúng sẽ tạo một chuỗi ký tự (Token) và gửi lại cho người dùng. 

Gửi yêu cầu: Những lần sau, người dùng gửi Token này trong phần Header của yêu cầu: 
Authorization: Bearer <mã_token_tại_đây> 

Xác thực: Laravel kiểm tra Token, nếu hợp lệ mới cho phép truy cập dữ liệu. 

Bước tiếp theo: Hãy sử dụng Postman để gọi thử các API bạn vừa viết. Khi bạn thấy dữ liệu JSON hiện lên màn hình, đó là lúc bạn đã thực sự làm chủ được Backend API! 

 

 

PHẦN 4: XÂY DỰNG VÀ KIỂM THỬ RESTFUL API 

 

Bài : GIỚI THIỆU DEMO RESTFUL API 

Sau khi đã xây dựng hệ thống RESTful API ở các phần trước, bước tiếp theo là kiểm chứng cách API hoạt động trong thực tế. 

Trong phần này, chúng ta xây dựng một trang web đơn giản bằng HTML và JavaScript thuần để đóng vai trò là Client. Trang web này sẽ gửi yêu cầu đến API và hiển thị dữ liệu nhận được. 

Điều này giúp minh họa rõ các nguyên lý quan trọng: 

RESTful API hoạt động độc lập với giao diện  

Frontend và Backend tách rời hoàn toàn  

Dữ liệu được trao đổi thông qua HTTP  

 

Bài : LIÊN KẾT GIỮA WEB DEMO VÀ HỆ THỐNG API 

Web demo trong phần này không phải là một hệ thống độc lập, mà là một Client dùng để kiểm thử và minh họa cho RESTful API đã được xây dựng ở các phần trước. 

Cụ thể: 

Ở Phần 1 – 3, hệ thống Backend đã được xây dựng, cung cấp các API như:  

/api/posts  

/api/posts/{id}  

Trong Phần 4, file demo.html sẽ gửi các yêu cầu HTTP đến các API này để lấy và thao tác dữ liệu.  

Do đó, để web demo hoạt động, cần đảm bảo: 

Hệ thống API đã được triển khai và đang chạy  

Các endpoint API có thể truy cập được  

 

Bài : XÂY DỰNG WEB DEMO (HTML + JAVASCRIPT) 

 

1.1 Tên và mục đích web demo 

Trang web demo có tên: 

Mini Blog RESTful API Demo 

Chức năng chính: 

Xem danh sách bài viết  

Thêm bài viết mới  

Xóa bài viết  

Mục tiêu của web demo là minh họa cách một ứng dụng Client giao tiếp với Server thông qua RESTful API. 

 

1.2 Vị trí file demo 

File demo.html được đặt trong thư mục public của project để có thể truy cập thông qua trình duyệt. 

Ví dụ cấu trúc thư mục: 

project/ 
├── app/ 
├── routes/ 
├── public/ 
│    └── demo.html 

Truy cập thông qua: 

http://localhost:8000/demo.html 

Việc đặt file trong thư mục public giúp web demo và API cùng chạy trên một domain, tránh các vấn đề liên quan đến bảo mật (CORS). 

 

1.3 Tạo file demo 

Trong phần này, xây dựng file demo.html đóng vai trò là Client để tương tác với RESTful API đã xây dựng ở các phần trước. 

Giao diện HTML chỉ mang tính chất tối thiểu để nhập dữ liệu và hiển thị kết quả, phần xử lý chính được thực hiện bằng JavaScript thông qua các request HTTP. 

 

a. Khung giao diện 

<h1>Mini Blog RESTful API Demo</h1> 
 
<input type="text" id="title" placeholder="Tiêu đề"> 
<input type="text" id="content" placeholder="Nội dung"> 
 
<button onclick="addPost()">Thêm</button> 
<button onclick="loadPosts()">Tải danh sách</button> 
 
<ul id="posts"></ul> 

 

b. Khai báo endpoint 

const API_URL = 'http://127.0.0.1:8000/api/posts'; 

Endpoint này tương ứng với resource posts đã xây dựng trong hệ thống. 

 

c. Lấy danh sách dữ liệu (GET) 

async function loadPosts() { 
    const res = await fetch(API_URL); 
    const data = await res.json(); 
 
    let html = ''; 
    data.data.forEach(post => { 
        html += ` 
            <li> 
                ${post.title} 
                <button onclick="deletePost(${post.id})">Xóa</button> 
            </li> 
        `; 
    }); 
 
    document.getElementById('posts').innerHTML = html; 
} 

Mapping RESTful: 

GET /api/posts → lấy danh sách resource  

Dữ liệu trả về dạng JSON → đúng chuẩn REST  

Client chỉ render dữ liệu  

 

d. Tạo mới dữ liệu (POST) 

async function addPost() { 
    const title = document.getElementById('title').value; 
    const content = document.getElementById('content').value; 
 
    await fetch(API_URL, { 
        method: 'POST', 
        headers: { 
            'Content-Type': 'application/json' 
        }, 
        body: JSON.stringify({ title, content }) 
    }); 
 
    loadPosts(); 
} 

Mapping RESTful: 

POST /api/posts → tạo resource mới  

Body gửi JSON → đúng chuẩn trao đổi dữ liệu  

 

e. Xóa dữ liệu (DELETE) 

async function deletePost(id) { 
    await fetch(`${API_URL}/${id}`, { 
        method: 'DELETE' 
    }); 
 
    loadPosts(); 
} 

Mapping RESTful: 

DELETE /api/posts/{id} → xóa resource  

URI định danh tài nguyên cụ thể  

 

f. Tổng kết 

File demo.html đóng vai trò là Client: 

Gửi request HTTP đến API  

Nhận và hiển thị dữ liệu  

Không chứa logic nghiệp vụ  

Các hàm JavaScript tương ứng trực tiếp với các HTTP methods trong RESTful API, thể hiện rõ sự ánh xạ giữa thao tác phía Client và hành động trên tài nguyên. 

 

 

1.4 Kết quả thực hiện 

Sau khi hoàn thành web demo, tiến hành chạy hệ thống và ghi nhận kết quả thông qua các thao tác thực tế. 

Hình 1: Giao diện ban đầu của hệ thống  

Hình 2: Kết quả gọi API lấy danh sách bài viết  

Hình 3: Thao tác thêm mới bài viết thông qua API  

Hình 4: Thao tác xóa bài viết thông qua API  

Thông qua các kết quả trên có thể thấy: 

Web demo đã gửi các request HTTP đến API và nhận dữ liệu trả về  

Các thao tác CRUD (GET, POST, DELETE) đều hoạt động đúng  

Dữ liệu được trao đổi dưới dạng JSON và hiển thị lại trên giao diện  

Điều này cho thấy sự tương tác giữa Client và Server đã được thực hiện đúng theo mô hình RESTful API. 

 

 

Bài : KIỂM THỬ API BẰNG Postman 

Để kiểm tra hoạt động của API một cách độc lập với giao diện, chúng ta sử dụng công cụ Postman. 

 

2.1 Kiểm thử GET /api/posts 

Method: GET  

URL: http://localhost:8000/api/posts  

Kết quả trả về: 

{ 
  "data": [...] 
} 

Hình 5: Kết quả gọi API GET trong Postman  

 

2.2 Kiểm thử POST /api/posts 

Method: POST  

URL: http://localhost:8000/api/posts  

Body:  

{ 
  "title": "Test API", 
  "content": "Demo Postman" 
} 

Kết quả: 

Status: 201 (Created)  

Hình 6: Tạo mới bài viết bằng POST  

 

2.3 Kiểm thử DELETE /api/posts/{id} 

Method: DELETE  

URL: http://localhost:8000/api/posts/{id}  

Kết quả: 

Status: 204 (No Content)  

Hình 7: Xóa bài viết bằng DELETE  

 

Bài : ÁNH XẠ NGUYÊN LÝ RESTFUL API 

 

3.1 Nguyên lý Client – Server 

Client: Web demo (HTML + JavaScript)  

Server: RESTful API  

Hai thành phần hoạt động độc lập và giao tiếp thông qua HTTP. 

 

3.2 Nguyên lý Stateless 

Mỗi request được gửi từ Client: 

fetch(API_URL) 

→ Server xử lý độc lập, không lưu trạng thái trước đó. 

 

3.3 Sử dụng HTTP Methods 

GET → Lấy dữ liệu  

POST → Tạo mới  

DELETE → Xóa  

 

3.4 Resource và URI 

/api/posts  

/api/posts/{id}  

 

3.5 Định dạng JSON 

const data = await res.json(); 

Dữ liệu trao đổi dưới dạng JSON. 

 

3.6 Tách biệt Frontend và Backend 

Backend chỉ cung cấp dữ liệu  

Frontend xử lý giao diện  

 

Kết luận 

Thông qua việc xây dựng web demo và kiểm thử API bằng Postman, có thể thấy: 

RESTful API hoạt động đúng theo nguyên lý  

Client và Server tách rời hoàn toàn  

Dữ liệu được trao đổi dưới dạng JSON  

Web demo không hoạt động độc lập mà phụ thuộc vào hệ thống API đã xây dựng ở các phần trước 

 

 