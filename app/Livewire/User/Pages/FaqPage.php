<?php

namespace App\Livewire\User\Pages;

use Livewire\Component;

class FaqPage extends Component
{
    public function render()
    {
        return view('livewire.user.pages.faq-page', [
            'faqsInfo' => self::faqs(),
        ])->layout('layouts.app');
    }

    public static function faqs(): array
    {
        $appName = config('app.name');
        $coinName = 'coins';
        return [
            'general' => [
                [
                    'question' => 'Are surveys reliable?',
                    'answer' => "All surveys on $appName are secure. Any information you provide in the surveys is kept anonymous, and the survey providers implement numerous measures to guarantee the safety of the surveys. We do not have access to the details you enter, as only the survey administrators can view and manage this information."
                ],
                [
                    'question' => "What steps do I need to take to begin?",
                    'answer' => "Getting started with $appName is simple and quick! You can register by entering your email or by using your Google or Steam account. Once registered, navigate to the \"Tasks\" page where you’ll find a variety of tasks to complete. Just pick the offers you wish to take on to begin."
                ],
                [
                    'question' => "What exactly are $coinName?",
                    'answer' => "$coinName serve as the currency within $appName, reflecting your account balance. They can also be represented in U.S. dollars, where 1000 $coinName equate to $1.00."
                ],
                [
                    'question' => "What are the rules for chat?",
                    'answer' => "Please refrain from advertising or spamming, including sharing referral links. Stick to English in the chat, except in designated language channels. Avoid harassment, insults, or provocation towards others. Do not share inappropriate links, and please do not beg or solicit money. Violating these chat guidelines could lead to your account being muted or even suspended temporarily or permanently."
                ],
                [
                    'question' => "What is $appName all about?",
                    'answer' => "$appName is an online platform that allows you to earn money by using applications and participating in surveys. We collaborate with top market research firms and apps to facilitate your online earning experience. $coinName you earn on EarnLab can be redeemed for PayPal credits, cryptocurrency, gift cards, and more."
                ],
                [
                    'question' => "Who manages the offer/survey walls displayed?",
                    'answer' => "$appName partners with leading market research firms to provide our users with a comprehensive array of offers and surveys. We act as a mediator, enabling you to earn money online effortlessly."
                ],
            ],
            'earn' => [
                [
                    'question' => "What is the best way to earn $coinName?",
                    'answer' => "To maximize your $coinName earnings, we recommend focusing on our \"Featured Tasks\" which provide substantial rewards. Visit the \"Tasks\" page, where you can explore numerous tasks to complete. Choose the ones that interest you to get started."
                ],
                [
                    'question' => 'Where can I fill out surveys?',
                    'answer' => "Surveys can be completed on both your PC and mobile devices. Simply head over to our \"Earn\" page to find all available survey providers."
                ],
                [
                    'question' => "Why did I receive fewer $coinName than promised?",
                    'answer' => "You might have been disqualified from completing a survey. Some offer walls still provide a few $coinName for your efforts, even if you don't qualify."
                ],
                [
                    'question' => "Why didn’t I receive any $coinName for my survey?",
                    'answer' => "Several factors could result in no $coinName being awarded. You may have been disqualified during the survey. If a survey was incorrectly configured, it may affect payment. Additionally, payouts might be delayed. Check your transaction history, and if you still haven't received your $coinName, reach out to the support team of the offer wall."
                ],
                [
                    'question' => "Why is my completed task on hold?",
                    'answer' => "Certain tasks may be automatically held based on various criteria like account status and task value. However, you can provide proof of completion through our live support to expedite the release of your hold."
                ]
            ],
            'withdraw' => [
                [
                    'question' => "How do I withdraw my $coinName?",
                    'answer' => "To withdraw your $coinName, navigate to the \"Withdraw\" section in your account dashboard. From there, select your preferred withdrawal method, enter the required information, and confirm your withdrawal. Please ensure that your account is verified to facilitate the process."
                ],
                [
                    'question' => 'How long does a withdrawal take?',
                    'answer' => "Withdrawal times vary depending on the method chosen. Typically, e-wallet withdrawals are processed within 24 hours, while bank transfers may take up to 3-5 business days. You can check the status of your withdrawal in the \"Transaction History\" section."
                ],
                [
                    'question' => 'Where can I find my voucher codes?',
                    'answer' => "Your voucher codes can be found in the \"Vouchers\" section of your account. If you have received promotional codes through email or notifications, you can also view them in your inbox or notifications panel."
                ],
            ],
            'account' => [
                [
                    'question' => 'How do I get verified?',
                    'answer' => "To get verified, you need to complete the verification process in your account settings. This usually involves providing personal information and uploading necessary documents such as a government-issued ID and proof of address. Once submitted, our team will review your application, and you will be notified of the status via email."
                ],
                [
                    'question' => 'Why is my balance in the negatives (-)?',
                    'answer' => "A negative balance usually indicates that you have incurred fees or penalties that exceed your available balance. This could be due to recent transactions, withdrawals, or account maintenance fees. To resolve this, please review your transaction history and ensure that your account is in good standing."
                ],
            ],
            'policies' => [
                [
                    'question' => 'Am I authorized to have multiple accounts?',
                    'answer' => "Typically, having multiple accounts is against our policy as it can lead to unfair advantages and disrupt the integrity of our platform. If you have specific circumstances that require multiple accounts, please contact our support team for clarification and approval."
                ],
                [
                    'question' => 'Can I run surveys on behalf of other users?',
                    'answer' => "Running surveys on behalf of other users is generally prohibited. Each user must complete surveys using their own account to ensure accurate and fair results. Violating this policy may result in account suspension or termination."
                ],
                [
                    'question' => 'Am I allowed to use VPNs?',
                    'answer' => "Using VPNs is not allowed as it can lead to account verification issues and potential abuse of our platform. We recommend accessing our services from your original IP address to avoid any complications."
                ],
            ],
        ];
    }
}
