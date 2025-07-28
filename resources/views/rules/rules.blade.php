@extends('layouts.app')


@section('title') rules @endsection


@section('content')
{!! breadcrumbs(['rules' => 'rules']) !!}

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="tabbable" id="tabs-494493">
				<ul class="nav nav-tabs" style="border-color: #26282a;">
					<li class="nav-item">
						<a class="nav-link active show" href="#tab1" data-toggle="tab">Section 1: SPECIES RULES</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#tab2" data-toggle="tab">Section 2: POUCHER RULES</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab3" data-toggle="tab">Section 3: DISCORD/CONDUCT</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab4" data-toggle="tab">Section 4: BREEDING SLOTS</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab5" data-toggle="tab">Section 5: OFFICIAL ARTISTS</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab6" data-toggle="tab">Section 6: DEREGISTERED DESIGNS</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab7" data-toggle="tab">Section 7: LORE RULES</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab8" data-toggle="tab">Section 8: AUCTION/ADOPT</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab9" data-toggle="tab">Section 9: FURDONS</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            S P E C I E S &nbsp R U L E S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <h6 style="text-indent: 20px;">These are the Official rules of the Species, Alladons.</h6>
                                                <br/>
                                                <ol><strong>
                                                    <li>Alladons are a Closed species, meaning you can only obtain one through purchasing official adopts, auctions, breeding, or obtaining a previously owned alladon from another user. You cannot make your own nor sell adopts. All previously owned transactions need to be confirmed by both involved parties.</li><br/>
                                                    <li>Alladons are a strictly 16+ species due to the nature of detail in their biology (and some of the artwork that comes with them). You are welcome to participate in the discord and species, but you will not be allowed to buy an alladon, companions, or breeding slots until you are 16+. We now require all users to be verified before participating in the server.</li><br/>
                                                    <li>If you no longer connect with your alladon and wish to sell, trade, or gift it to someone else; that user must meet the 16+ requirement for the species and must not be on the species blacklist. If you are found to be transferring or helping someone to obtain an alladon who is not allowed, you are at risk of receiving a permanent warning or species blacklist/ban (at admin discretion; situation dependent). NOTE: You cannot ask for more than the alladon's worth (base value + art). See The Shelter opening post for expanded rules on this subject.</li><br/>
                                                    <li>An alladon's gender will be locked once decided and put on the official registry. This is normally locked at age up (for bred alladons), or breeding for the first time (for adopts). Customs are locked to the gender decided on at the creation of the don. You may change the gender (ie they identify as Female if their sex is Male), but the breeding sex cannot change once chosen.</li><br/>
                                                    <li>Your alladon is it's registry number. If you are deregistered for whatever reason then your alladons have been disowned as part of our species and will no longer be classed as an alladon. I will ask that you change the species but that is up to you. If you receive an alladon/poucher with an invalid or fake registry number, please contact me or an admin on our Discord Server so we can deal with it appropriately.</li><br/>
                                                    <li>Selling a registered alladon to a deregistered user is currently prohibited.</li><br/>
                                                    <li>Selling a registered alladon to a blacklisted user is prohibited and risks potentially being blacklisted (pending investigation from the admin team).</li><br/>
                                                    <li>Currently re-registering Alladons is CLOSED! So if you purchase a de-regged design with the intention of bringing it back into the community, you will not be able to!</li><br/>
                                                    <li>Admins do not enforce personal TOS.</li><br/>
                                                    <li>Currently, alladons can not be made into other species. (Meme creatures are fine!)</li><br/>
                                                    <li>Alladons must stay alladons - if you wish to change your alladons' species, they must be de-registered (memes do not count to this category).</li><br/>
                                                    <li>De-registering is a "final decision" to leave the alladon server and community - If you decide to de-register, all alladons within your possession will be taken off the reg and no longer classed as "alladons." You will be expected to leave the server upon completion of the de-registration. If you de-reg and attempt to obtain a regged alladon (through another user helping you or on your own), you may be blacklisted and banned from the species. If you don't wish to leave the community entirely, you are welcome to take a break! Leaving the server will not de-reg you! If you wish to know more please visit our Deregistering Rules page.</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
					<div class="tab-pane" id="tab2">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            P O U C H E R &nbsp R U L E S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <h6 style="text-indent: 20px;">'Pouchlings' and 'Whelp' are the terms given to premature alladons. If adopted, there are a few limitations on these designs until they can be used as normal alladons.</h6>
                                                <br/>
                                                <ol><strong>
                                                    <li>Owners must have a pouchling for 3 months real time before it can be considered as an adult and rebased to officially be classed as an adult. Rebasing can only be done by an Official Age Up Artist. Rebasing will be the official step into adult hood. They do not need to be rebased after the 3 month time period is up, this is up to the owner to decide!</li><br/>
                                                    <li>Whelps/Pouchlings/Pouchers cannot be bred from until the 3 month time limit is up and they have been rebased as an adult.</li><br/>
                                                    <li>Pouchlings will be given a code that relates to their genetic line. They also now recieve their official number as well on the website.</li><br/>
                                                    <li>Prior to Age Up, you CANNOT "promise" breeding slots to users. This means you cannot plan to give someone a slot while the don is still a poucher, official slots are only recognized if the don is an adult. Failure to follow this rule will result in an Official Warning, and continued neglect will result in slot/breeding restriction and blacklisting.</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab3">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            D I S C O R D / C O N D U C T
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <h6 style="text-indent: 20px;">The main hub for this species is our discord servers (Official and Community)! We're there to send information, exchange ideas, help develop our characters and trade art and artists that we get of our alladons! I want to create a trustworthy community that isn't toxic or elitist like some other species... regardless of your artistic skills, your large/small wallet, you're all equal in this community. The server is an informal way of getting to know your fellow dons and a great way to make new friends and find artists/creative minds to help you.</h6>
                                                <br/>
                                                <h6>These following rules are compulsory and failure to abide by them after an official warning will result from being removed from the server (whether this be temporary or permanent):</h6><br/>
                                                <ol><strong>
                                                    <li>Do not belittle, harass, overly critique when not welcomed or just otherwise make other members uncomfortable in the server. Banter is fine, but please remember that this community is for people to enjoy their alladons.</li><br/>
                                                    <li>Do not shame someone for their artwork. We're here to develop each other as artists and as creators.</li><br/>
                                                    <li>Make sure chat is relevant - check the channel descriptions for the rules for where you're speaking!</li><br/>
                                                    <li>Don't cause or bring drama into the server. We may ask people to take disputes into DMs or open a ticket to keep the peace for other people. If something has happened, open a ticket to speak to the admin team!</li><br/>
                                                    <li>If you're having 'banter' - i.e playfully picking on a character with defects, make sure you know when to stop. There is a line between banter and bullying. Anybody specifically upsetting you within the chat? Tell them to stop first, if they continue please report them to an admin. We cannot always act upon disputes unless we've been made aware beforehand that a particular party is not listening to requests to leave the situation alone.</li><br/>
                                                    <li>Act mature in the chat. If you can't take a little banter, or if you can't tolerate someone who's speaking, don't respond for a while and wait for them to move on.</li><br/>
                                                    <li>RPing in chat is not allowed. Don't be annoying- there's no definition, just be considerate of others and their comfort!</li><br/>
                                                    <li>Some NSFW will be shared within the chat. These SHOULD BE WARNED before hand, spoilered with content warnings, and in the appropriately indicated channels, but beyond this there is no obligation for you to view or take part.</li><br/>
                                                    <li>Begging in chat is disallowed. If you would like someone to claim for you on an adopt or a breeding slot, personal message a friend. Claiming for others is still allowed, however people will have to offer their service first. Anyone found begging will be reminded of this rule. Continuous breakers will receive an official warning.</li><br/>
                                                    <li>We release certain alladons with snipe guards/don-blocks to help get new members of the server into the species. Whilst frustrating for some, this is for the good of the newbies in the server, as it can be really difficult to get an alladon. Anybody thought to be trying to bypass the Snipe-Guard rules by enrolling out of server acquaintances to get access to dons will be faced with the possibility of a temporary adoption ban, or an official warning. This is down to the discretion of the admin team.</li><br/>
                                                    <li>Spamming advertisements (including stud slots, commission slots, alladon sales etc) are not allowed in the server. Please remember that these channels are for people to scroll through and find what they want, and isn't a first come first serve board.</li><br/>
                                                    <li>Do not pressure the alladon artists to open slots or take your commission. Artists will open when they are available! Please note that begging is not only against our species TOS but also against many of the artists individual TOS: this includes little backhand comments such as "I never get picked" or "I wish I could have [x] but I'm too poor" etc. If you're saying something to get someone's opinion of you changed, please consider that this might break someone's, or the species, TOS.</li><br/>
                                                    <li>You must be contactable by at least one of the admin team. Charlies, Betas and Alphas cannot be blocked on discord for security reasons.</li><br/>
                                                    <li>Do not mini-mod. If you see someone breaking a rule, please report it to an admin. Do not attempt to handle situations yourself - this is in place to avoid public conflicts within the community. Let the admins do their jobs in handling rule breaks.</li><br/>
                                                </ol></strong>
                                                <h6>It's not a rule, but we encourage everyone to remember what it was like when they first joined the alladon server and how hard it was to get your ticket don.</h6><br/>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab4">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            B R E E D I N G &nbsp S L O T S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <h6 style="text-indent: 20px;">One of the ways to get an Alladon is through breeding! You can purchase, trade, and gift breeding/stud slots with others to get pouchers! Below are the rules surrounding breeding slots: </h6>
                                                <br/>
                                                <ol><strong>
                                                    <li>Registered Alladons: Only slots in our Stud Confirmations discord channel will be accepted by Poucher Artists (for FF slots see below). These slot posts must be unedited and include any companions (if applicable) in the post to be useable. Please view the channel rules for specifics.</li><br/>
                                                    <li>Deregistered Designs: Once an alladon is de-regged, all gifted and traded slots are considered void, unless in Stud Confirmations. If you de-reg your alladons, it is your responsibility to contact those you sold slots to if you no longer wish for them to be used.</li><br/>
                                                    <li>Full Fur Slots: FF Slots are no longer accepted in Stud Confirmations. They are now being tracked on site to protect users from getting a slot that doesn't exist outside the 6 alloted slots. Owners of FFs must open a ticket to inform Admins of slots being sold/traded/gifted so we can input that on the dons page. Users with FF slots, when using them in PA openings, please state which slot you are using (ie Slot 1, 2, 3, etc).</li><br/>
                                                    <li>You cannot sell or trade any slots with any don before they are aged up. If you sell a poucher, you cannot request a breed slot with that don in the future as part of the sales agreement. Any previous plans are not official and does not need to be upheld by the new owner or any consecutive owners if the poucher is sold on.</li><br/>
                                                    <li>Companions can be used at the level of your choosing. If no level is specified, artists will use the highest level possible.</li><br/>
                                                    <li>Free slots can be posted in freebie studs, however they are only available for free use until the owner removes the post. Dons may be removed at any time. Slots cannot be claimed until opening. Free to use slots can be either for everyone or free to use owners. No other limitations may be imposed.</li><br/>
                                                    <li>Alladons cannot breed to their parents, grandparents, or full siblings.</li><br/>
                                                </ol></strong>
                                                <h6>Please read more about Stud Rules in breeding slot channels: Studbook, Freebie Studs, The Blackmarket, Stud Raffles, and Stud Confirmations!</h6><br/>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab5">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            O F F I C I A L &nbsp A R T I S T S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <ol><strong>
                                                    <li>You cannot request edits on pouchers or age ups to the PA/AUA. These are not custom OCs. Artists follow rules, standards, and styles when making dons to follow the species rules and fit in our breeding system. If something looks wrong, please inform/ask the artist! Hinting/asking/commenting for personal preference is not allowed. Doing so will result in a warning and further events in blacklisting from breeding/age ups.</li><br/>
                                                    <li>Individual artists have the right to refuse customers.</li><br/>
                                                    <li>Do not pressure the alladon artists to open slots or take your commission. Artists will open when they are available! (Read more about this in DISCORD/CONDUCT)</li><br/>
                                                    <li>Teeth cannot be changed on rebase if a teeth type was specified on age up (ie. T14 on the ref) If no teeth type is specified, teeth can be changed on rebase.</li><br/>
                                                    <li>Age up artists have a three month window to complete a design from acceptance of payment in full. If the age up is not complete in the expected time, the user is allowed to transfer the age up to another artist. The artist is allowed to transfer the age up to another artist within the three months if they feel they cannot complete the work in time, but we request that users refrain from asking to transfer to another artist before the allotted three months. You do not have to transfer the age up to another artist and are welcome to allow the current artist more time if needed.</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab6">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            D E R E G I S T E R E D &nbsp D E S I G N S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <h6 style="text-indent: 20px;">If you decide to deregister/de-reg your Alladon(s), you are saying you want no more to do with the Alladon Community and are expected to leave without the ability for you or those designs to come back. This is a last resort decision.</h6>
                                                <br/>
                                                <ol><strong>
                                                    <li>We ask prior to deregging you get any paid for breeding slots and commissions managed, either by contacting the member you purchased from about a refund or by selling them back into the community, these slots will be voided once you leave. It's important to know and understand that any free/gift slots you have given out will be considered null, bar raffled slots and any slots currently placed in Stud Confirmations.</li><br/>
                                                    <li>If you have any Ouni, they are not to be removed from the community so before you leave we ask that they be gifted/traded to another member still within the server. Any companions removed from the species will be permanently removed and cannot be sold back into the community.</li><br/>
                                                    <li>A reminder that once deregged the designs will NOT hold alladon value, they revert to Beth’s base character design value of £20.</li><br/>
                                                    <li>We are under no obligation to take back deregged dons. Especially if that character has had a chequered history once outside of the community. If you chose to purchase a deregged don, please be aware we are currently NOT reregging designs. If you are buying dons outside of the server PLEASE CHECK IF THEY ARE REGGED BEFORE EXCHANGING ANYTHING!</li><br/>
                                                    <li>Registered users within the alladon community can own deregistered designs, however they will not be re-registered within the community. This means if they are a poucher, they cannot be aged up, and they cannot be posted in the alladon chats of the community server to avoid confusion.</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab7">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            L O R E &nbsp R U L E S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <h6 style="text-indent: 20px;">Alladons, once purchased, are free for you to use as your own character. I'm not stone-hard set on 'they must follow these rules' because dons are designed to be independent and malleable to any situation. Just remember to credit ThatAlbinoThing as the creator of the species/design. I do not condone changes in the design of your character once adopted unless discussed with me prior or commissioned by myself. Small things like scars, tattoos etc I have no quarrel with. If you want to have the do something that is completely polar opposite to what the species norm is, give it a reason to be that way. Development is important to any character! These are my only solid conditions of owning an alladon.</h6>
                                                <br/>
                                                <h6>If you're interested in following to lore that comes with alladons or mingling with the rest of us, here are the conditions:</h6><br/>
                                                <ol><strong>
                                                    <li>Never take an alladon's thing.</li><br/>
                                                    <li>They are driven by instinct. They can feel the polar opposite, but the urge to act as described for the species will always be there.</li><br/>
                                                    <li>Lower scoring alladons will be intimidated/respect higher scoring alladons naturally.</li><br/>
                                                    <li>They must seek companionship of sorts - within the dynamic must be a leader (whether that's the alladon or not).</li><br/>
                                                    <li>They're carnivores - vegetation will be tolerated in small doses but will eventually make the don sick.</li><br/>
                                                    <li>Pouchlings are always going to have the family ties to their parents - speak with the discord about working out where they came from and their history!</li><br/>
                                                    <li>D E V E L O P M E N T... Dive into the inner workings of your don, WHY do they tick the way they do?</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab8">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            A U C T I O N S  /  A D O P T S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <br/>
                                                <ol><strong>
                                                    <li>Auction dons do NOT hold the value of the winning bid. If resold they hold normal content value + mut points if applicable.</li><br/>
                                                    <li>You may bid/enter a raffle on behalf of someone, but you must state this so staff knows.</li><br/>
                                                    <li>Payment plans MUST be discussed beforehand, failure to do so can result in being blacklisted from auctions.</li><br/>
                                                    <li>You MUST have the ability to pay if you win an auction, failure to do so can result in auction blacklisting.</li><br/>
                                                    <li>Raffle adopts are rolled in order of reg number/posted and only one per person, so if you enter multiple and win an earlier one, your entries for the next raffles don't count.</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
                    <div class="tab-pane" id="tab9">
				        <br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <h5 class="card-header">
                                            F U R D O N S
                                        </h5>
                                        <div class="card-body">
                                            <p class="card-text" style="font-family: Nunito, sans-serif;">
                                                <br/>
                                                <ol><strong>
                                                    <li>Furdons or Full Furs (FF) are restricted to having a max of 6 FF pouchers to maintain their status as very rare in the species.</li><br/>
                                                    <li>When a FF breeds to make a FF poucher, that uses 1 of their 6 slots. If all 6 are used, that furdon can no longer make full fur pouchers.</li><br/>
                                                    <li>When breeding 2 FFs, a FF slot is used for both of them.</li><br/>
                                                    <li>3/4 fur hybrids do not have this limitation - meaning a single 3/4 fur hybrid can breed as many full furs as you want.</li><br/>
                                                    <li>However in order to get a full fur kid from a 3/4 hybrid, that hybrid must breed with a full furdon (3/4 and 3/4 will not result in a full fur) - this will use up a full fur kid slot from the furdon parent. Keep this in mind.</li><br/>
                                                    <li>Note there are a group of FFs that were made and breeding prior to this rule being established, which have made FFs that don't count towards their 6 slots. FFs bred prior to Aug 28, 2020 don't count towards these slots.</li><br/>
                                                    <li>If a FF slot is used and rolls a snon (and the snon is chosen), the furdon does not use one of their 6 FF slots. If the slot wasn't used by the owner, that FF slot is returned to the owner.</li><br/>
                                                </ol></strong>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated <em>July 27, 2025</em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection