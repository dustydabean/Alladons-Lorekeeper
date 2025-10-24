@extends('layouts.app')


@section('title') Guide @endsection


@section('content')
{!! breadcrumbs(['Guide' => 'guide']) !!}

<div class="card" style="font-family: Roboto Condensed, serif; background-color: #1e1e1f5c;">
    <div class="card-header" style="background-color: #15161896;">
        <h2 style="text-align: center; text-transform: uppercase; font-weight: bold; color: #ffffff;">Guides</h2>
    </div>
    <div class="card-body" style="text-align: center; color: #ffffff; text-transform: uppercase; padding-bottom: 10px;">
        <h6>Here are all the Guides on how to do things organized by topic!</h6>
    </div>
</div>

<br/>

<div class="card" id="accordion" style="font-family: Roboto Condensed, serif; background-color: #1e1e1f5c;">
    <div class="card-header" style="font-size: 1.4rem; text-align: center; font-weight: bold; text-transform: uppercase; background-color: #15161896;">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
            Prompts
        </a>
    </div>
    <div class="card-collapse collapse in" id="collapse1">
        <div class="card-body" style="font-size: 1.0rem; color: #ffffff;">
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
            <div class="spoiler text-left">
                <div class="spoiler-toggle">Art Criteria Guide</div>
                    <div class="spoiler-text">
                        <h6 style="font-size: 1.2rem;">Here is a breakdown for the Art Criteria and descriptions for what qualify!</h6>
                        <hr>
                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Character Visibility</h3>
                                        <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">How much the character is visible in the artwork</div>
                                        <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">100%</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Full character is visible</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">75%</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Most of the character is visible</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">50%</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Halfbody visible</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">25%</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Headshot/bust visible</span></div>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Finish Level</h3>
                                        <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">How refined the lines are</div>
                                            <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Rough Sketch</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Lines are sketched</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Clean Sketch</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Sketch is cleaned up, not messy</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Lined / Lineless</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Art is lined or a lineless work</span></div>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Number of Characters</h3>
                                    <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">How many characters are in the piece</div>
                                        <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">1 character</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Only 1 character</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">2 characters</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">2 characters, both with the same level of visibility</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">3 characters</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">3 characters, all with the same level of visibility</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">4+ characters</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">4 or more characters, with the same level of visibility</span></div>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Shading</h3>
                                    <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">Level of shading</div>
                                        <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Unshaded</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">No shading</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Minimal Shading</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Few areas with simple shading</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Fully Shaded</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Most of the character is shaded</span></div>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Background</h3>
                                <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">Level of background detail</div>
                                    <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">No / Simple / Photo BG</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">No BG, simple BG, or photo used for BG</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Pattern / Abstract</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Self made pattern or as simple BG</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Detailed BG</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">BG depicts a scene or is complex</span></div>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Color</h3>
                                <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">Level of Color in the piece</div>
                                    <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">No Color</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Piece has no color, only black/white</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Rough Color</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Messy color blocked in, simplified markings</span></div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Clean Color</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">Neatly colored, all markings present</span></div>
                            </div>
                        </div>

                        <div class="d-flex mt-3">
                            <div style="flex: 2;">
                                <h3 class="mb-0">Other</h3>
                                <div class="parsed-text mt-3 pl-2 py-2" style="border-left: 4px solid lightgrey">Only use if any of these apply, if not leave blank.</div>
                                    <h4 class="mt-2">Options:</h4>
                            </div>
                        </div>
                        <div class="pl-4">
                            <div class="d-flex align-items-center">
                                <h5 class="mt-2">Animated</h5><span class="mx-1"> · </span><div class="text-secondary"><span class="display-currency">The piece is animated or a GIF</span></div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<br/>
<div class="card" id="accordion" style="font-family: Roboto Condensed, serif; background-color: #1e1e1f5c;">
    <div class="card-header" style="font-size: 1.4rem; text-align: center; font-weight: bold; text-transform: uppercase; background-color: #15161896;">
        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">
            Transfers and Trades
        </a>
    </div>
    <div class="card-collapse collapse in" id="collapse2">
        <div class="card-body" style="font-size: 1.0rem; color: #ffffff;">
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
