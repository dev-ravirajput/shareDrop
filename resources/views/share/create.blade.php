<form method="POST" action="{{ route('share.store') }}" enctype="multipart/form-data">
    @csrf
    <textarea name="text" placeholder="Drop text here..."></textarea>
    <input type="file" name="file">
    <button type="submit">Share</button>
</form>
