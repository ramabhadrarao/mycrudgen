$(document).ready(function() {
    // Function to handle the modal
    function showModal() {
        $('#uploadModal').fadeIn();
    }

    function hideModal() {
        $('#uploadModal').fadeOut();
    }

    // Event listeners for opening and closing the modal
    $(document).on('click', '.photo-input, .attachment-input', function() {
        showModal();
        window.currentFileInput = $(this); // Store reference to the current file input
    });

    $('#closeModal').click(function() {
        hideModal();
    });

    // Handle the file upload
    $('#uploadForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: '../includes/attachmentupload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    alert('File uploaded successfully.');
                    window.currentFileInput.val(data.fileName); // Set the file name to the input
                    hideModal();
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function() {
                alert('File upload failed.');
            }
        });
    });

    // Add class to inputs for photos and attachments
    $('input[type="text"][id="photo"]').addClass('photo-input').attr('readonly', 'readonly');
    $('input[type="text"][id="attachment"]').addClass('attachment-input').attr('readonly', 'readonly');
    $('input[type="text"][id="document_path"]').addClass('attachment-input').attr('readonly', 'readonly');
    $('input[type="text"][id="certificate_document"]').addClass('attachment-input').attr('readonly', 'readonly');

    
});
