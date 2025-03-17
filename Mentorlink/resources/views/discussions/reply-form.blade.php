<div class="reply-box">
    <form action="{{ route('discussion.reply', $discussionId) }}" method="POST">
        @csrf
        <textarea name="message" placeholder="Write your reply..." required rows="3"></textarea>
        <button type="submit">Reply</button>
    </form>
</div>
