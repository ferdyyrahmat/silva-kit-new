<?php

namespace App\Http\Controllers\System\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SanctumTokenController extends Controller
{
    /**
     * Generate a new Personal Access Token for the user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'token_name' => 'required|string|max:100',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $token = $user->createToken($request->token_name);

        audit_log("Created new API Token: {$request->token_name}");

        return response()->json([
            'success'    => true,
            'message'    => 'API Token created successfully! Please copy your token key now.',
            'redirect'   => route('v1.profile.index'),
            'token_key'  => $token->plainTextToken,
            'token_id'   => $token->accessToken->id,
            'name'       => $token->accessToken->name,
            'created_at' => $token->accessToken->created_at->format('Y-m-d H:i'),
        ]);
    }

    /**
     * Revoke a specific Personal Access Token.
     */
    public function destroy(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->tokens()->where('id', $id)->delete();

        audit_log("Revoked API Token ID #{$id}");

        return response()->json([
            'success'  => true,
            'message'  => 'API Token revoked successfully.',
            'redirect' => route('v1.profile.index')
        ]);
    }
}
