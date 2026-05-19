<?php
$dir = "/tmp/recordings";
$message = null;
$messageType = "success";

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "delete") {
    $filename = basename($_POST["filename"] ?? "");
    $filePath = $dir . "/" . $filename;

    if ($filename === "" || !str_ends_with($filename, ".mp4")) {
        $message = "Ungültiger Dateiname.";
        $messageType = "danger";
    } elseif (!is_file($filePath)) {
        $message = "Die Datei wurde nicht gefunden.";
        $messageType = "warning";
    } elseif (unlink($filePath)) {
        $sourceFile = str_ends_with($filename, ".flv.mp4") ? $dir . "/" . substr($filename, 0, -4) : null;
        if ($sourceFile && is_file($sourceFile)) {
            unlink($sourceFile);
        }
        $message = "Die Datei wurde gelöscht: " . $filename;
    } else {
        $message = "Die Datei konnte nicht gelöscht werden.";
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Video-Übersicht</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery (für Bootstrap Modal und Button-Events) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .video-thumbnail {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <h1 class="text-center mb-4">Gespeicherte Videos</h1>
    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?>" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div id="video-grid" class="row g-3">
        <?php
        $videos = array_values(array_filter(glob("$dir/*.mp4"), 'is_file'));
        foreach ($videos as $index => $file) {
            $filename = basename($file);
            $videoUrl = "/videos/" . rawurlencode($filename);
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 video-item" data-index="<?= $index ?>" style="<?= $index >= 16 ? 'display: none;' : '' ?>">
                <div class="card h-100">
                    <div class="card-body p-2">
                        <video class="img-fluid video-thumbnail" data-bs-toggle="modal" data-bs-target="#videoModal" data-video="<?= htmlspecialchars($videoUrl) ?>" muted>
                            <source src="<?= htmlspecialchars($videoUrl) ?>" type="video/mp4">
                            Dein Browser unterstützt kein Video.
                        </video>
                    </div>
                    <div class="card-footer text-center small">
                        <div class="text-truncate mb-2" title="<?= htmlspecialchars($filename) ?>"><?= htmlspecialchars($filename) ?></div>
                        <div class="d-flex gap-2 justify-content-center">
                            <a class="btn btn-sm btn-outline-primary" href="<?= htmlspecialchars($videoUrl) ?>" download="<?= htmlspecialchars($filename) ?>">Download</a>
                            <form method="post" class="m-0 delete-form">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="filename" value="<?= htmlspecialchars($filename) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Löschen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php if (count($videos) > 16): ?>
        <div class="text-center mt-4">
            <button id="loadMore" class="btn btn-primary">Zeig mir mehr</button>
        </div>
    <?php endif; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Video Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
            </div>
            <div class="modal-body">
                <video id="modalVideo" controls style="width: 100%;">
                    <source src="" type="video/mp4">
                    Dein Browser unterstützt kein Video.
                </video>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let itemsToShow = 16;
    const totalItems = <?= count($videos) ?>;
    
    $("#loadMore").click(function () {
        itemsToShow += 16;
        $(".video-item").each(function () {
            if ($(this).data("index") < itemsToShow) {
                $(this).fadeIn();
            }
        });
        if (itemsToShow >= totalItems) {
            $("#loadMore").hide();
        }
    });

    // Modal Video laden
    $('#videoModal').on('show.bs.modal', function (event) {
        const trigger = $(event.relatedTarget);
        const videoSrc = trigger.data('video');
        const modalVideo = $("#modalVideo");
        modalVideo.find("source").attr("src", videoSrc);
        modalVideo[0].load();
    });

    // Video stoppen, wenn Modal geschlossen wird
    $('#videoModal').on('hidden.bs.modal', function () {
        const modalVideo = $("#modalVideo")[0];
        modalVideo.pause();
        modalVideo.currentTime = 0;
    });

    $(".delete-form").on("submit", function () {
        const filename = $(this).find("input[name='filename']").val();
        return confirm(`Datei wirklich löschen?\n\n${filename}`);
    });
});
</script>

</body>
</html>
