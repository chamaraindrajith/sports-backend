@include('partials.css')
@include('partials.js')

<table class="table table-dark table-bordered table-striped">
    <thead>
        <tr>
            <th> id</th>
            <th> sid</th>
            <th> sport_id </th>
            <!-- <th> ground </th> -->
            <!-- <th> teams1</th>
            <th> teams2 </th> -->
            <th> team_id_teams1</th>
            <th> team_id_teams2 </th>
            <!-- <th> name </th> -->
            <!-- <th> start_date </th> -->
            <!-- <th> end_date</th> -->
            <th> category_id </th>
            <!-- <th> category_slug </th> -->
            <!-- <th> category_name </th> -->
            <!-- <th> created_at</th> -->
            <!-- <th> updated_at </th> -->

            <th> status_text </th>
            <th> status </th>
            <th> Team1 Score </th>
            <th> Team2 Score </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($games as $game)
            <tr>
                <td> {{ $game->id }} </td>
                <td> {{ $game->stage_id }} </td>
                <td> {{ $game->sport_id }} </td>
                <!-- <td> {{ $game->ground }} </td> -->

                <td> {{ json_encode(unserialize($game->team_id_teams1)) }} </td>
                <td> {{ json_encode(unserialize($game->team_id_teams2)) }} </td>
   
                <!-- <td> {{ $game->start_date }} </td> -->
                <!-- <td> {{ $game->end_date }} </td> -->
                <td> {{ $game->category_id }} </td>

                
                <!-- <td> {{ $game->created_at }} </td> -->
                <!-- <td> {{ $game->updated_at }} </td> -->

                <td> {{ $game->status_text }} </td>
                <td> {{ $game->status }} </td>
<?php /*
                @if ($game->t1i1d == 1) 
                     {{ $t1i1d = 'd' }}
                @else
                     {{ $t1i1d = '' }}
                @endif
                @if ($game->t1i2d == 1) 
                     {{ $t1i2d = 'd' }}
                @else
                     {{ $t1i2d = '' }}
                @endif
                @if ($game->t2i1d == 1) 
                     {{ $t2i1d = 'd' }}
                @else
                     {{ $t2i1d = '' }}
                @endif
                @if ($game->t2i2d == 1) 
                     {{ $t2i2d = 'd' }}
                @else
                     {{ $t2i2d = '' }}
                @endif


                <td> {{ $game->t1i1r }}/{{ $game->t1i1w }}{{ $t1i1d }} & {{ $game->t1i2r }}/{{ $game->t1i2w}}{{ $t1i2d }} </td>
                <td> {{ $game->t2i1r }}/{{ $game->t2i1w }}{{ $t2i1d }} & {{ $game->t2i2r }}/{{ $game->t2i2w}}{{ $t2i2d }} </td>
*/?>
            </tr>
        @endforeach
    </tbody>
</table>
