<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PlayerTurnController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'players_count' => 'nullable|integer|min:1|max:26',
            'turns_count' => 'nullable|integer|min:1',
            'start_player' => 'nullable|regex:/^[a-zA-Z]$/',
        ]);

        $playersCount = $request->get('players_count', 3);
        $turnsCount = $request->get('turns_count', 3);
        $startPlayer = strtoupper($request->get('start_player', 'A'));


        $players = range('A', chr(ord('A') + $playersCount - 1)); // Generate players based on players count
        $startIndex = array_search($startPlayer, $players); // Find the start player index from the generated players array

        $turns = []; // Initialize turns array
        $k = 0; // Initialize the index of the first element in turns array to be able to reverse the order of single turn

        // loop over the turns
        for ($i = 0; $i < $turnsCount; $i++) {

            // Check if the first turn hasn't been ended yet
            if (count($turns) < $playersCount) {
                $turnOrder = []; // Initialize turn order array

                // loop over the players
                for ($j = 0; $j < $playersCount; $j++) {
                    $playerIndex = ($startIndex + $j) % $playersCount; // Get the player index through the start index and the players count
                    $turnOrder[] = $players[$playerIndex]; // Append the player to the turn order
                }

                $startIndex = ($startIndex + 1) % $playersCount; // Get the start index of the next player
            } else {
                $turnOrder = array_reverse($turns[$k]); // Reverse the turn order
                ++$k; // Increment the index of the first element in turns array
            }

            $turns[] = $turnOrder; // append the turn order to turns array
        }

        return response()->json($turns);
    }
}
