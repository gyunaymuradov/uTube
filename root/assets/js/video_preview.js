var canvas, context, w, h, video;
w = 600;
h = 400;
function  previewVideo(element) {
    var preview = document.getElementById('preview');
    video = document.getElementById('videoPreview');
    video.src = URL.createObjectURL(element.files[0]);
    video.onend = function(e) {
        URL.revokeObjectURL(this.src);
    };

    canvas = document.getElementById('canvas');
    context = canvas.getContext('2d');
    video.addEventListener('loadedmetadata', function() {
        canvas.width = w;
        canvas.height = h;
        context.fillRect(0, 0, w, h);
        context.drawImage(video, 0, 0, w, h);
        document.getElementById('thumbnailIMG').src = canvas.toDataURL();
        document.getElementById('thumbnailSRC').value = canvas.toDataURL();
    }, false);

    preview.style.display = "block";

}

function createThumbnail() {
    context.fillRect(0, 0, w, h);
    context.drawImage(video, 0, 0, w, h);
    document.getElementById('thumbnailIMG').src = canvas.toDataURL();
    document.getElementById('thumbnailSRC').value = canvas.toDataURL();
}