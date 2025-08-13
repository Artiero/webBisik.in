<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.toggle-comments');
        toggles.forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const targetId = toggle.getAttribute('data-target');
                const commentSection = document.getElementById(targetId);
                if (commentSection.style.display === 'none') {
                    commentSection.style.display = 'block';
                } else {
                    commentSection.style.display = 'none';
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.react').forEach(btn => {
            btn.addEventListener('click', function() {
                const emoji = this.dataset.emoji;
                const postId = this.closest('.actions').dataset.postId;

                fetch('./reaction_handler.php', { // pastikan path benar
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `problem_id=${encodeURIComponent(postId)}&emoji=${encodeURIComponent(emoji)}`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const container = document.querySelector(`.actions[data-post-id="${postId}"]`);
                            container.querySelectorAll('.react').forEach(span => {
                                const type = span.dataset.emoji;
                                span.querySelector('.count').textContent = data.counts[type] || 0;
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Gagal menyimpan reaksi.', 'error');
                        }
                    })
                    .catch(err => console.error('Error:', err));
            });
        });
    });
</script>