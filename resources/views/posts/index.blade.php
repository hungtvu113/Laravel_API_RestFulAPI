<!DOCTYPE html>
<html>
<head>
    <title>Danh sách bài viết</title>
</head>
<body>
    <h1>Danh sách bài viết</h1>
    
    <a href="{{ route('posts.create') }}">Tạo bài viết mới</a>
    
    @if(session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @endif
    
    @foreach($posts as $post)
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->content }}</p>
        <hr>
    @endforeach
</body>
</html>
