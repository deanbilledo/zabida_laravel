    <?php

    namespace App\Http\Controllers;

    use App\Mail\ContactFormSubmitted;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Mail;

    class ContactController extends Controller
    {
        public function show()
        {
            return view('contact', ['currentPage' => 'contact']);
        }

        public function submit(Request $request)
        {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:190'],
                'message' => ['required', 'string', 'max:5000'],
                'website' => ['prohibited'],
            ], [
                'name.required' => 'Please tell us your name.',
                'email.required' => 'Please enter an email address so we can reply.',
                'email.email' => 'That email address doesn\'t look right — please double-check it.',
                'message.required' => 'Please enter a message.',
            ]);

            try {
                Mail::to('zabida_prod_admin@zabida.org')->send(new ContactFormSubmitted(
                    senderName: $validated['name'],
                    senderEmail: $validated['email'],
                    messageBody: $validated['message'],
                ));

                Log::info('ZABIDA contact form submission sent', ['name' => $validated['name'], 'email' => $validated['email']]);

                return back()->with('contact_status', 'success')
                    ->with('contact_message', 'Thanks, '.$validated['name'].' — your message has been sent. We\'ll get back to you soon.');
            } catch (\Throwable $e) {
                Log::error('ZABIDA contact form failed to send', ['error' => $e->getMessage()]);

                return back()->withInput()
                    ->with('contact_status', 'error')
                    ->with('contact_message', 'Something went wrong sending your message. Please try again, or email us directly at zabidamail.ph@gmail.com.');
            }
        }
    }