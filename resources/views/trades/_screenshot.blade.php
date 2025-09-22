<!-- Screenshot viewer modal (one time) -->
<div class="modal fade" id="screenshotModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document" style="max-width:900px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Screenshots</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
      </div>
      <div class="modal-body">
        <div id="screenshotCarousel" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators" id="screenshotIndicators"></ol>
          <div class="carousel-inner" id="screenshotInner"></div>
          <a class="carousel-control-prev" href="#screenshotCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </a>
          <a class="carousel-control-next" href="#screenshotCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </a>
        </div>
      </div>
      <div class="modal-footer">
        <a href="#" id="screenshotMiniPageLink" class="btn btn-outline-primary btn-sm" target="_blank">Open mini page</a>
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
