@extends('app')
@section('title', 'Flight Assignments')

@section('content')
  <div class="row row-cols-lg-2">
    {{-- Left Column --}}
    <div class="col-lg-8">
      @if(count($assignments) === 0)
        <div class="alert alert-primary p-1 px-2 fw-bold">@lang('DSpecial::common.no_assignments')</div>
      @endif
      @foreach($assignments as $group => $tas)
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DSpecial::common.fl_assignments') | {{ Carbon::create()->day(1)->month($group)->format('F') }}
            <i class="fas fa-hourglass-half float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 text-start table-responsive">
          <table class="table table-sm table-striped table-borderless mb-0 text-start text-nowrap align-middle">
            <tr>
              <th class="text-center">#</th>
              <th>@lang('DSpecial::common.flight_no')</th>
              <th>@lang('airports.departure')</th>
              <th>@lang('airports.arrival')</th>
              <th class="text-center">@lang('DSpecial::common.block_time')</th>
              <th class="text-center">&nbsp;</th>
            </tr>
            @foreach($tas->sortBy('assignment_order', SORT_NATURAL) as $as)
              <tr>
                <td class="text-center" style="width: 3%">{{ $as->assignment_order }}</td>
                <td class="col-1">
                  @if($as->flight)
                    <a href="{{ route('frontend.flights.show', [$as->flight->id]) }}">
                      {{ optional($as->flight->airline)->code.' '.optional($as->flight)->flight_number }}
                    </a>
                  @endif
                </td>
                <td>
                  @if($as->flight)
                    @if(Theme::getSetting('flights_flags'))
                      <img class="img-mh25 mx-1" title="{{ strtoupper(optional($as->flight->dpt_airport)->country) }}"
                        src="{{ public_asset('/image/flags_new/'.strtolower(optional($as->flight->dpt_airport)->country).'.png') }}" alt=""/>
                    @endif
                    <a href="{{ route('frontend.airports.show', [$as->flight->dpt_airport_id]) }}" target="_blank">
                      {{ optional($as->flight->dpt_airport)->full_name ?? $as->flight->dpt_airport_id}}
                      @if(filled(optional($as->flight->dpt_airport)->iata)){{ ' ('.$as->flight->dpt_airport->iata.')'}}@endif
                    </a>
                  @endif
                </td>
                <td>
                  @if($as->flight)
                    @if(Theme::getSetting('flights_flags'))
                      <img class="img-mh25 mx-1" title="{{ strtoupper(optional($as->flight->arr_airport)->country) }}"
                        src="{{ public_asset('/image/flags_new/'.strtolower(optional($as->flight->arr_airport)->country).'.png') }}" alt=""/>
                    @endif
                    <a href="{{ route('frontend.airports.show', [$as->flight->arr_airport_id]) }}" target="_blank">
                      {{ optional($as->flight->arr_airport)->full_name ?? $as->flight->arr_airport_id}}
                      @if(filled(optional($as->flight->arr_airport)->iata)){{ ' ('.$as->flight->arr_airport->iata.')'}}@endif
                    </a>
                  @endif
                </td>
                <td class="text-center">
                  @if($as->flight)
                    @minutestotime($as->flight->flight_time)
                  @endif
                </td>
                <td class="text-center" style="width: 3%;">
                  @if($as->completed)
                    @if(filled($as->pirep_id))
                      <a href="{{ route('frontend.pireps.show', [$as->pirep_id]) }}"><i class="fas fa-check-circle text-success"></i></a>
                    @else
                      <i class="fas fa-check-circle text-success"></i>
                    @endif
                  @else
                    <i class="fas fa-exclamation-circle text-danger"></i>
                  @endif
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      @endforeach
    </div>
    {{-- Right Column --}}
    <div class="col-lg-4">
      @if(count($assignments) > 0 && $dbasic === true)
        <div class="row mb-1">
          <div class="col">
            @widget('DBasic::Map', ['source' => 'assignment'])
          </div>
        </div>
      @endif
      @if($stats)
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DSpecial::common.personal_stats')
              <i class="fas fa-dna float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0 table-responsive">
            @foreach ($stats as $month => $stat)
              @if($month === 'Overall')
                <table class="table table-sm table-borderless table-striped align-middle text-center mb-0">
                  <tr>
                    <th class="col-4">@lang('DSpecial::common.assignments')</th>
                    <th class="col-4">@lang('DSpecial::common.completed')</th>
                    <th class="col-4">@lang('DSpecial::common.earnings')<span class="small">&sup1;</span></th>
                  </tr>
                  <tr>
                    <td>{{ $stat['total'] }}</td>
                    <td>{{ $stat['completed'] }}</td>
                    <td>{{ $stat['earnings'] }}</td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <div class="progress" height="20px">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['ratio'] }}%;" aria-valuenow="{{ $stat['ratio'] }}" aria-valuemin="0" aria-valuemax="100">{{ $stat['ratio'].'%' }}</div>
                      </div>
                    </td>
                  </tr>
                </table>
              @elseif($month != 'Overall')
                <hr>
                <table class="table table-sm table-borderless table-striped align-middle text-center mb-0">
                  <tr>
                    <th class="text-start" colspan="3">{{ $month }}</th>
                  </tr>
                  <tr>
                    <th class="col-4">@lang('DSpecial::common.assignments')</th>
                    <th class="col-4">@lang('DSpecial::common.completed')</th>
                    <th class="col-4">@lang('DSpecial::common.earnings')<span class="small">&sup1;</span></th>
                  </tr>
                  <tr>
                    <td>{{ $stat['total'] }}</td>
                    <td>{{ $stat['completed'] }}</td>
                    <td>{{ $stat['earnings'] }}</td>
                  </tr>
                  <tr>
                    <td colspan="3">
                      <div class="progress" height="20px">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['ratio'] }}%;" aria-valuenow="{{ $stat['ratio'] }}" aria-valuemin="0" aria-valuemax="100">{{ $stat['ratio'].'%' }}</div>
                      </div>
                    </td>
                  </tr>
                </table>
              @endif
            @endforeach
          </div>
          <div class="card-footer p-0 px-1 text-start small">
            <b>&sup1;</b> @lang('DSpecial::common.earning_note')
          </div>
        </div>
      @endif
      @ability('admin', 'admin-user')
        <div class="row">
          @if(!$sys_check)
            <div class="col">
              <div class="text-end">
                <form class="form" method="post" action="{{ route('DSpecial.assignments_manual') }}">
                  @csrf
                  <input type="hidden" name="curr_page" value="{{ url()->full() }}">
                  <button class="btn btn-sm bg-success p-0 px-1 text-black" type="submit">Assign Monthly Flights</button>
                </form>
              </div>
            </div>
          @else
            <div class="col">
              <div class="float-start">
                <form class="form" method="post" action="{{ route('DSpecial.assignments_manual') }}">
                  @csrf
                  <input type="hidden" name="curr_page" value="{{ url()->full() }}">
                  <button class="btn btn-sm bg-warning p-0 px-1" type="submit">Re-Assign Current Month</button>
                </form>
              </div>
              <div class="float-end">
                <form class="form" method="post" action="{{ route('DSpecial.assignments_manual') }}">
                  @csrf
                  <input type="hidden" name="curr_page" value="{{ url()->full() }}">
                  <input type="hidden" name="resetmonth" value="true">
                  <button class="btn btn-sm bg-danger p-0 px-1 text-black" type="submit" onclick="return confirm('Are you REALLY sure ? This will DELETE and re-assign flights !')">Delete & Re-Assign Current Month</button>
                </form>
              </div>
            </div>
          @endif
        </div>
      @endability
    </div>
  </div>
@endsection
