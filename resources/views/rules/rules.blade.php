@extends('layouts.app')


@section('title') rules @endsection


@section('content')
{!! breadcrumbs(['rules' => 'rules']) !!}

<div class="card-mut">This page is WIP! Once finished there will be an announcement!</div>

<br>
<br>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="tabbable" id="tabs-494493">
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a class="nav-link active show" href="#tab1" data-toggle="tab">Section 1: SPECIES RULES</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#tab2" data-toggle="tab">Section 2: POUCHER RULES</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link" href="#tab3" data-toggle="tab">Section 3: DISCORD/CONDUCT</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<br>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card-mut">
                                        <h5 class="card-header">
                                            S P E C I E S &nbsp R U L E S
                                        </h5>
                                        <div class="card-mut-body">
                                            <p class="card-text">
                                                RULES
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated DATE
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
                                            <p class="card-text">
                                                RULES
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated DATE
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
                                            <p class="card-text">
                                                RULES
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            Last Updated DATE
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