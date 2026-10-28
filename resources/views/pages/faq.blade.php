@extends('layouts.app')

@section('title', 'Frequently Asked Questions — CropCircle')

@section('content')
<div class="container py-5">
    <div class="max-w-700 mx-auto text-center mb-5">
        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill mb-3">Help Center</span>
        <h1 class="fw-bold">Frequently Asked Questions</h1>
        <p class="text-muted">Everything you need to know about pre-ordering, pickups, and selling on CropCircle.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion shadow-sm rounded-4 overflow-hidden border-0" id="faqAccordion">
                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How does pre-ordering and pickup work?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary">
                            Browse products from local farmers, choose the quantity you need, and proceed to checkout. During checkout, you select a pickup day and time window for each farmer stall. Your farmer packs your basket and keeps it ready for you at the market stall.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            When and how do I pay?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary">
                            Payment is made in person directly at the farmer's market stall when you collect your order. You can pay via cash, local card terminal, or any offline payment method accepted by the seller.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Can I order from multiple farmers at the same time?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary">
                            Yes! You can put items from different growers into one cart. During checkout, CropCircle cleanly splits your order by farmer so that you can select the appropriate pickup window and instructions for each specific stall.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            Can I cancel an order?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary">
                            You can cancel any order while its status is still <strong>Pending</strong>. Once the farmer has accepted the order and started harvesting or packing, cancellation is restricted to avoid food waste.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold py-3" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                            How do I register as a farmer?
                        </button>
                    </h2>
                    <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-secondary">
                            Click "Join CropCircle", select "Register as Farmer/Grower", and provide your farm name, location, and description. An administrator reviews your profile to verify your local agricultural operation. Once approved, you can immediately publish products and receive orders.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
