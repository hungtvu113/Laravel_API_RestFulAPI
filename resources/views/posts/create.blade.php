<!DOCTYPE html>
<html>
<head>
    <title>Tạo bài viết mới</title>
</head>
<body>
    <h1>Tạo bài viết mới</h1>
    
    <form action="{{ route('posts.store') }}" method="POST">
        @csrf
        <div>
            <input type="text" name="title" placeholder="Tiêu đề" required>
        </div>
        <div>
            <textarea name="content" placeholder="Nội dung" required></textarea>
        </div>
        <button type="submit">Lưu bài viết</button>
    </form>
    
    <br>
    <a href="{{ route('posts.index') }}">Quay lại danh sách</a>
</body>
</html>
