@push('css')
  <style>
    .view i{
      font-size: 25px;
      border: 1px solid #000;
      border-radius: 3px;
      padding: 0px 3px;
    }
    .view.active i{
      background: linear-gradient(to right,#0db5c8 0,#089bab 100%);
      color: #fff;
      border-color: #089bab;
    }
    .job_card{
      cursor: pointer;
      color: #089bab !important;
    }
  </style>
@endpush

<div class="modal right fade" id="right_modal_common" tabindex="-1" role="dialog" aria-labelledby="right_modal_common">
  <div class="modal-dialog modal-lg right-modal-width" role="document" > 
    <div class="modal-content">
      <div class="modal-header">
        <a class="view prev_btn-job" data-toggle="tooltip" data-dismiss="modal" data-placement="top" title="" data-original-title="Back to Report">
      <i class="las la-chevron-left"></i>
    </a>
        <h5 class="modal-title right-modal-title text-center capitalize" id="modal-title-common"> &nbsp; </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding-top: 0;">
        <div class="col-12 h-min-400">
          <div class="modal-content-result" id="content-result-common"></div>
        </div>
      </div>
      
    </div>
  </div>
</div>
