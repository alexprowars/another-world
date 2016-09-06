<div class="mt-element-step">
	<div class="step-line">
		<div class="col-md-4 mt-step-col first {% if success %}done{% endif %}">
			<div class="mt-step-number bg-white">1</div>
			<div class="mt-step-title uppercase font-grey-cascade">Dependencies</div>
		</div>
		<div class="col-md-4 mt-step-col">
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
			<span class="caption-subject font-red-sunglo bold uppercase">Step 1</span>
		</div>
	</div>
	<div class="portlet-body form">
		{{ flashSession.output() }}
		<form action="{{ url('step1/') }}" method="post" class="form-horizontal form-row-seperated">
			{% for key, value in params %}
				<input type="hidden" name="{{ key }}" value="{{ value }}">
			{% endfor %}
			<div class="form-body">
				{% for item in required %}
					<div class="form-group last">
						<label class="control-label col-md-3">{{ item['title'] }}</label>
						<div class="col-md-9">
							<div class="form-control-static">
								{% if item['status'] %}
									<div class="font-green-jungle">{{ item['current'] }}</div>
								{% else %}
									<div class="font-red">{{ item['current'] }}</div>
								{% endif %}
							</div>
							<span class="help-block">{{ item['desc'] }}</span>
						</div>
					</div>
				{% endfor %}
			</div>
			{% if success %}
				<div class="form-actions">
					<button type="submit" class="btn green">Next step</button>
				</div>
			{% endif %}
		</form>
	</div>
</div>