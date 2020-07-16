<?php

namespace App\Http\Controllers\API;

use API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\FeedbackSent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Notification;

class FeedbackController extends Controller
{
    public function send(Request $request)
    {
        $user = auth()->user();

        $recipient = config('app.feedback_recipient');

        $request->validate([
            'message' => ['bail', 'string', 'required']
        ]);

        $message = htmlspecialchars($request->post("message"));

        Notification::route('mail', $recipient)->notify(new FeedbackSent($user, $message));

        return API::response(200, "Feedback sent successfully", []);
    }
}
