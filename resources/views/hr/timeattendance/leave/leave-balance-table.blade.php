<style type="text/css">
    table.leave-balance-table {
        border: 1px solid #d1d1d1; 
        width: 95%; 
        border-collapse: collapse; 
        text-align: center
    }
    table.leave-balance-table tr{padding: 20px}
    table.leave-balance-table td,th{border: 1px solid #d1d1d1; padding: 5px 0px;}
    .popover-block-container .popover-icon {
      background: none;
      color: none;
      border: none;
      padding: 0;
      outline: none;
      cursor: pointer;
    }
    .popover-block-container .popover-icon i {
      color: #04a0b2;
      text-align: center;
      margin-top: 4px;
    }

    .popover-header {
      display: none;
    }

    .popover {
      max-width: 306.6px;
      border-radius: 6px;
      border: none;
      box-shadow: 2px 2px 10px 2px #d1d1d1;
      color: #000;

    }
    .popover-body hr{
        border-color: #000 !important;
    }
    .popover-body br{
        content: "";
          margin: 2em;
          display: block;
        font-size: 35%;
    }


    .popover-body {
        padding: 20px 49.4px 24px 24px;
        z-index: 2;
        line-height: 1.2;
        letter-spacing: 0.1px;
        
    }
    .popover-body .popover-close {
      position: absolute;
      top: 5px;
      right: 10px;
      opacity: 1;
    }
    .popover-body .popover-close .fa {
      font-size: 16px;
      font-weight: bold;
      color: #04a0b2;
    }
</style>
<table class="leave-balance-table" >
    <thead>
        <tr>
            <th>Leave</th>
            <th>Entitled</th>
            <th>Enjoyed</th>
            <th>Available</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-left pl-3 font-weight-bold">Casual</td>
            <td>{{$balance->casual->total??0}}</td>
            <td>{{ $balance->casual->enjoyed??0 }}</td>
            <td>{{ $balance->casual->total - $balance->casual->enjoyed }}</td>
        </tr>
        <tr>
            <td class="text-left pl-3 font-weight-bold">Sick</td>
            <td>{{$balance->sick->total??0}}</td>
            <td>{{ $balance->sick->enjoyed??0 }}</td>
            <td>{{ $balance->sick->total - $balance->sick->enjoyed }}</td>
        </tr>
        <tr>
            <td class="text-left pl-3 font-weight-bold">
                Earned <div class="popover-block-container" style="display: inline-block;">
                      {{-- <button tabindex="0" type="button" class="popover-icon" data-popover-content="#unique-id" data-toggle="popover"  data-placement="right" title="click to see Earned leave rule">
                        <i class="fa fa-info-circle text-primary"></i>
                      </button>
                      <div id="unique-id" style="display:none;">
                        <div class="popover-body">
                            <p>A employee can enjoy maximum <b>50%</b> of earned leave.</p>
                        </div>
                      </div> --}}
                    </div>
            </td>
            <td>{{$balance->earned->total??0}}</td>
            <td>{{$balance->earned->enjoyed??0}}</td>
            <td>
                @if(\Carbon\Carbon::parse($employee->as_doj)->age >= 1)
                {{round((($balance->earned->total) - $balance->earned->enjoyed),2) }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="text-left pl-3 font-weight-bold">Special</td>
            <td></td>
            <td>{{$balance->special??0}}</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

<script type="text/javascript">
    $("[data-toggle=popover]").popover({
            html : true,
            trigger: 'focus',
            content: function() {
                var content = $(this).attr("data-popover-content");
                return $(content).children(".popover-body").html();
            }
        });
</script>