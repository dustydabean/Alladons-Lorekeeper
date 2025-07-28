@extends('layouts.app')


@section('title') Guide @endsection


@section('content')
{!! breadcrumbs(['Guide' => 'guide']) !!}

<div class="card" id="accordion" style="font-family: Roboto Condensed, serif;">
    <div class="card-header" style="font-size: 1.4rem; text-align: center; font-weight: bold; text-transform: uppercase;">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
            Prompts
        </a>
    </div>
    <div class="card-collapse collapse in" id="collapse1">
        <div class="card-body" style="font-size: 1.0rem;">
            <h6 style="font-size: 1.0rem;">Prompts are a fun and creative way to interact with the community and on our site! You can either submit art or writing to any of the prompts we have!</h6>
            <p> </p>
            <h5>RULES</h5>
            <ul>
                <li>
                    <h6>Everyone is welcome to do prompts regardless of skill level! We only look for the minimum requirements to be met!</h6>
                </li>
                <li>
                    <h6>Plagiarism/Tracing/Copying/Bases/AI will not be tolerated and will result blacklisting from Prompts</h6>
                </li>
                <li>
                    <h6>Make sure to check for further Prompt specific requirements in the Prompt Details</h6>
                </li>
            </ul>
            <h5>ART REQUIREMENTS</h5>
            <ul>
                <li>
                    <h6>You must either own the Alladon or have permission to draw someone else’s don (check their profile or ask the owner)</h6>
                </li>
                <li>
                    <h6>The Alladon must be recognizable as that don</h6>
                </li>
                <li>
                    <h6>Art must clearly depict the purpose of the prompt</h6>
                </li>
                <li>
                    <h6>Any pre-existing art, commissions, or trades cannot be submitted as prompt entries</h6>
                </li>
            </ul>
            <h5>WRITING REQUIREMENTS</h5>
            <ul>
                <li>
                    <h6>You must either own the Alladon or have permission to write about someone else’s don (check their profile or ask the owner)</h6>
                </li>
                <li>
                    <h6>The writing must be at least 400 words</h6>
                </li>
                <li>
                    <h6>We won’t judge based on grammar or spelling, but we do want to see your best effort</h6>
                </li>
                <li>
                    <h6>Writing must clearly depict the purpose of the prompt</h6>
                </li>
                <li>
                    <h6>Any pre-existing writing, commissions, or trades cannot be submitted as prompt entries</h6>
                </li>
            </ul>
            <h5>HOW TO SUBMIT</h5>
            <ol>
                <li>
                    <h6>If you would like to submit your art/writing so that it is visible on the site and in the Alladons personal gallery please start here, but if you don’t want it on the site skip to step 9</h6>
                </li>
                <li>
                    <h6>Go to GALLERY in the nav bar at the top, and select the gallery you want to submit to</h6>
                    <ul>
                        <li>
                            <h6>Specific Prompts will have separate galleries.</h6>
                        </li>
                    </ul>
                </li>
                <li>
                    <h6>Upload the Image or the Writing, give it a title, and any content warnings if necessary/applicable</h6>
                </li>
                <li>
                    <h6>Make sure the correct prompt is selected</h6>
                </li>
                <li>
                    <h6>Add any characters in the art/writing</h6>
                </li>
                <li>
                    <h6>Since this is for a prompt, there wouldn’t be any Collaborators, all art/writing should be done yourself</h6>
                </li>
                <li>
                    <h6>Same with Other Participants, this piece can’t be traded/commissioned/gifted</h6>
                </li>
                <li>
                    <h6>Hit Submit!</h6>
                </li>
                <li>
                    <h6>Go to the Prompt and hit Submit Prompt</h6>
                </li>
                <li>
                    <h6>If you submitted your prompt to the Gallery, select the piece in the Gallery URL dropdown. If you didn’t submit to the Gallery, paste a URL to the art in Submission URL or paste the writing piece in the comments</h6>
                    <ul>
                        <li>
                            <h6>To get a URL to your art, upload it to a site like TH, DA, Imgur, etc and make sure the link allows us to view it  </h6>
                        </li>
                    </ul>
                </li>
                <li>
                    <h6>For Criteria Rewards hit Add Criterion. If this is an Art piece select the Art Criteria or if its Writing select the Writing Criteria</h6>
                </li>
                <li>
                    <h6>Select the dropdowns for the categories in the Criteria that apply to your piece</h6>
                </li>
                <li>
                    <h6>Add any characters in the piece, you can also select to notify the owner of the prompt submission using their alladon</h6>
                </li>
                <li>
                    <h6>Make sure everything is correct, and confirm by clicking the checkboxes at the bottom and Submit!</h6>
                    <ul>
                        <li>
                            <h6>If you aren’t ready to submit, you can save it as a draft and come back to it later!</h6>
                        </li>
                    </ul>
                </li>
            </ol>
        </div>
    </div>
</div>
<br/>
<div class="card" id="accordion" style="font-family: Roboto Condensed, serif;">
    <div class="card-header" style="font-size: 1.4rem; text-align: center; font-weight: bold; text-transform: uppercase;">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
            Transfers and Trades
        </a>
    </div>
    <div class="card-collapse collapse in" id="collapse2">
        <div class="card-body" style="font-size: 1.0rem;">
            <h6 style="font-size: 1.0rem;">There are 2 ways to send ownership of an Alladon to another user: Transfers and Trades.</h6>
                <ul>
                    <li>
                        <h6>Transfers are for when a don is Sold or Gifted.</h6>
                        <ul>
                            <li>
                                <h6>Transfers are found on the don’s profile, at the bottom of the left hand sidebar.</h6>
                            </li>
                            <li>
                                <h6>If the don is Sold, state so in the Reason for Transfer along with the amount they were sold for</h6>
                            </li>
                            <li>
                                <h6>If the don is Gifted, state so in the Reason for Transfer</h6>
                            </li>
                            <li>
                                <h6>If the above isn’t followed your transfer will be rejected with the reason for you to fix it!</h6>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <h6>Trades is for when a don is Traded, regardless if the other side of the trade is onsite items or not.</h6>
                        <ul>
                            <li>
                                <h6>Trades are found in the navbar under Activity at the bottom of the dropdown.</h6>
                            </li>
                            <li>
                                <h6>Both parties will add the dons/items they are trading, and if there are any off site items in the trade (characters, art, etc) state that in the Proof of Terms.</h6>
                            </li>
                            <li>
                                <h6>Both parties will need to confirm their part of the trade before sending it off for staff approval.</h6>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <h6>If you need to transfer a don with someone who is not registered on the site or you are not on the site, then please open a ticket with proof of transfer (DM agreement) so we can change the ownership.</h6>
                    </li>
                </ul>
        </div>
    </div>
</div>

@endsection
