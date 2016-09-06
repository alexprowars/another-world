<div class="mt-element-step">
	<div class="step-line">
		<div class="col-md-4 mt-step-col first done">
			<div class="mt-step-number bg-white">1</div>
			<div class="mt-step-title uppercase font-grey-cascade">Dependencies</div>
		</div>
		<div class="col-md-4 mt-step-col {% if success %}done{% endif %}">
			<div class="mt-step-number bg-white">2</div>
			<div class="mt-step-title uppercase font-grey-cascade">Information</div>
		</div>
		<div class="col-md-4 mt-step-col last">
			<div class="mt-step-number bg-white">3</div>
			<div class="mt-step-title uppercase font-grey-cascade">Finish</div>
		</div>
	</div>
</div>

<div class="portlet light">
	<div class="portlet-title">
		<div class="caption">
			<i class="icon-equalizer font-red-sunglo"></i>
			<span class="caption-subject font-red-sunglo bold uppercase">Step 2</span>
		</div>
	</div>
	<div class="portlet-body form">
		{{ flashSession.output() }}
		<form action="{{ url('step2/') }}" method="post" class="form-horizontal form-row-seperated">
			<div class="form-body">
				<h3 class="form-section">Database</h3>
				<div class="form-group">
					<label class="control-label col-md-3">Host</label>
					<div class="col-md-9">
						<input type="text" name="db[host]" class="form-control" value="{{ params['db'] is defined ? params['db']['host'] : 'localhost' }}" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Username</label>
					<div class="col-md-9">
						<input type="text" name="db[username]" class="form-control" value="{{ params['db'] is defined ? params['db']['username'] : '' }}" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Password</label>
					<div class="col-md-9">
						<input type="password" name="db[password]" class="form-control" value="{{ params['db'] is defined ? params['db']['password'] : '' }}" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Database</label>
					<div class="col-md-9">
						<input type="text" name="db[database]" class="form-control" value="{{ params['db'] is defined ? params['db']['database'] : '' }}" autocomplete="off">
					</div>
				</div>
				<div class="form-group last">
					<label class="control-label col-md-3">Table prefix</label>
					<div class="col-md-9">
						<input type="text" name="db[prefix]" class="form-control" value="{{ params['db'] is defined ? params['db']['prefix'] : 'game_' }}" autocomplete="off">
					</div>
				</div>
				<h3 class="form-section">Information</h3>
				<div class="form-group">
					<label class="control-label col-md-3">Site name</label>
					<div class="col-md-9">
						<input type="text" name="app[title]" class="form-control" value="{{ params['app'] is defined ? params['app']['title'] : '' }}" autocomplete="off">
					</div>
				</div>
				<div class="form-group last">
					<label class="control-label col-md-3">Site email</label>
					<div class="col-md-9">
						<input type="email" name="app[email]" class="form-control" value="{{ params['app'] is defined ? params['app']['email'] : '' }}" autocomplete="off">
					</div>
				</div>
				<h3 class="form-section">Admin</h3>
				<div class="form-group">
					<label class="control-label col-md-3">Email</label>
					<div class="col-md-9">
						<input type="email" name="user[email]" class="form-control" value="{{ params['user'] is defined ? params['user']['email'] : '' }}" autocomplete="off">
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">Password</label>
					<div class="col-md-9">
						<input type="password" name="user[password]" class="form-control" value="" autocomplete="off">
					</div>
				</div>
				<div class="form-group last">
					<label class="control-label col-md-3">Confirm password</label>
					<div class="col-md-9">
						<input type="password" name="user[password_confirm]" class="form-control" value="" autocomplete="off">
					</div>
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn green">Next step</button>
			</div>
		</form>
	</div>
</div>