<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\WebsiteFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WebsiteFeedbackController extends Controller
{
    public function create(): View
    {
        return view('feedback.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:review,issue,suggestion'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'public_consent' => ['sometimes', 'accepted'],
        ]);

        $feedback = WebsiteFeedback::create([
            'user_id' => Auth::id(),
            'submitter_role' => Auth::user()->role,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'rating' => $validated['rating'] ?? null,
            'public_consent' => $validated['type'] === 'review' && $request->boolean('public_consent'),
        ]);

        foreach (User::where('role', 'admin')->pluck('id') as $adminId) {
            Notification::send(
                (int) $adminId,
                'New website feedback',
                Auth::user()->name . ' (' . Auth::user()->role . ') sent a ' . $feedback->type . ': ' . $feedback->subject,
                'feedback',
                '/admin/feedback'
            );
        }

        return redirect()->route('feedback.create')->with('success', 'Thank you. Your feedback was sent to the CropCircle admin team. Website reviews remain private unless you consent to public display and an admin features them.');
    }

    public function adminIndex(Request $request): View
    {
        $query = WebsiteFeedback::with('user')->latest();

        if (in_array($request->query('type'), ['review', 'issue', 'suggestion'], true)) {
            $query->where('type', $request->query('type'));
        }
        if (in_array($request->query('status'), ['new', 'reviewed', 'resolved'], true)) {
            $query->where('status', $request->query('status'));
        }

        $feedbackItems = $query->paginate(15)->withQueryString();
        $newFeedbackCount = WebsiteFeedback::where('status', 'new')->count();

        return view('admin.feedback.index', compact('feedbackItems', 'newFeedbackCount'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', 'in:new,reviewed,resolved']]);
        $feedback = WebsiteFeedback::findOrFail($id);
        $feedback->update([
            'status' => $validated['status'],
            'is_featured' => $validated['status'] === 'reviewed' ? $feedback->is_featured : false,
        ]);

        return back()->with('success', 'Website feedback status updated.');
    }

    public function toggleFeature(int $id): RedirectResponse
    {
        $feedback = WebsiteFeedback::findOrFail($id);

        if (!$feedback->is_featured && ($feedback->type !== 'review' || !$feedback->public_consent || $feedback->status !== 'reviewed')) {
            return back()->with('warning', 'Only reviewed website reviews with the submitter\'s public-display consent can be featured.');
        }

        $featured = !$feedback->is_featured;
        $feedback->update(['is_featured' => $featured]);
        ActivityLog::record(Auth::id(), 'website_feedback_feature_updated', ($featured ? 'Featured' : 'Unfeatured') . ' website feedback #' . $id . ' on the homepage.');

        return back()->with('success', $featured
            ? 'Consented website review featured on the homepage.'
            : 'Website review removed from the homepage; the original feedback remains private in the admin inbox.');
    }
}