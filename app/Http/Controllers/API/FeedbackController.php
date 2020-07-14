<?php

namespace App\Http\Controllers\API;

use API;
use Illuminate\Http\Request;
use App\Notifications\FeedbackSent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;

class FeedbackController extends Controller
{
    public function send(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'message' => ['bail', 'string', 'required']
        ]);

        $message = htmlspecialchars($request->post("message"));

        Notification::route('mail', env('FEEDBACK_RECIPIENT'))->notify(new FeedbackSent($user, $message));

        return API::response(200, "Feedback sent successfully", []);
    }
}
