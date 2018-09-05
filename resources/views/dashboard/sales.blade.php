@layout('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
	<div class="page-header">
		<div class="page-title">
			<h3>Dashboard</h3>
			<span>Hello there, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}! {{ Auth::user()->company_id }}</span>
		</div>
		<ul class="page-stats">
			<li>
				<div class="summary">
					<img src="/assets/img/logo.png" alt="logo" width="200px"/>
				</div>
			</li>
		</ul>
	</div>
@stop

@section('content')
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i></h4>
			</div>
			<div class="widget-content">
                    <div class="tabbable box-tabs">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#box_tab1" data-toggle="tab">Whiteboard</a></li>
<!--
							<li><a href="#box_tab4" data-toggle="tab">Top 50 Products (pcs)</a></li>
							<li><a href="#box_tab3" data-toggle="tab">Top 50 Products</a></li>
							<li><a href="#box_tab2" data-toggle="tab">Top 25 Customers</a></li>
							<li class="active"><a href="#box_tab1" data-toggle="tab">Statistics</a></li>
-->
							<li><a href="#box_tab2" data-toggle="tab">Overdue Invoices</a></li>
							<li><a href="#box_tab3" data-toggle="tab">Unpaid Invoices</a></li>
						</ul>
						<div class="tab-content">
<!--

							<div class="tab-pane active" id="box_tab1">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Coming Soon</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="box_tab2">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Coming Soon</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="box_tab3">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p>Coming Soon</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="box_tab4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">

                                        </div>
                                    </div>
                                </div>
                            </div>
-->

                            <div class="tab-pane" id="box_tab3">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-hover">
                                                <thead>
                                                    <th>Order #</th>
                                                    <th>C.O.N</th>
                                                    <th>Company Name</th>
                                                    <th>Open Amount</th>
                                                </thead>
                                                    @foreach($unpaid_orders as $unpaid_order)
                                                        <?php if($unpaid_order->open_amount <= 0){continue;};?>
                                                    <tr>
                                                        <td>{{$unpaid_order->order_no }}</td>
                                                        <td>{{$unpaid_order->customer_order_number}}</td>
                                                        <td>{{$unpaid_order->customer_name}}</td>
                                                        <td>{{$unpaid_order->open_amount}}</td>
                                                        <td><a href="/orders/show/{{$unpaid_order->id}}">View</a></td>
                                                    </tr>
                                                    @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="box_tab2">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-hover">
                                                <thead>
                                                    <th>Order #</th>
                                                    <th>C.O.N</th>
                                                    <th>Company Name</th>
                                                    <th>Open</th>
                                                    <th>Due</th>
                                                    <th>Days</th>
                                                </thead>
                                                    @foreach($overdue_invoices as $overdue_invoice)
                                                        <?php 
                                                            if($overdue_invoice->open_amount <= 0){continue;};
                                                            $order = Order::find($overdue_invoice->id);
                                                            $due   = $order->getDueDate();
                                                            if($due > date("Y-m-d")){
                                                                continue;
                                                            }
                                                        ?>
                                                    <tr>
                                                        <td>{{$overdue_invoice->order_no}}</td>
                                                        <td>{{$overdue_invoice->customer_order_number}}</td>
                                                        <td>{{wordwrap($overdue_invoice->customer_name,30,"<br />")}}</td>
                                                        <td>{{$overdue_invoice->open_amount}}</td>
                                                        <td>
                                                            {{$order->getDueDate() }}
                                                        </td>
                                                        <td>
                                                            {{$order->getDaysOverdue() }}
                                                        </td>
                                                        <td><a href="/orders/show/{{$overdue_invoice->id}}">View</a></td>
                                                    </tr>
                                                    @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane active" id="box_tab1">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table cellpadding="0" cellspacing="0" border="0" class="table table-hover">
                                                    <thead>
                                                        <th>Order #</th>
                                                        <th>Date</th>
                                                        <th>Company Name</th>
                                                        <th>Container</th>
                                                        <th>Est. Finish</th>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($whiteboards as $whiteboard)
                                                        <tr>
                                                            <td>{{$whiteboard->order_no}}</td>
                                                            <td>{{$whiteboard->order_date}}</td>
                                                            <td>{{$whiteboard->customer->customer_name}}</td>
                                                            <td>{{$whiteboard->container->name}}</td>
                                                            <td>{{$whiteboard->estimated_finish_date}}</td>
                                                            <td><a href="/orders/show/{{$whiteboard->id}}">View</a></td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
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

@stop
