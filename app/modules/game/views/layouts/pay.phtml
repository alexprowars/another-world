<? $this->view->partial('shared/city_header', Array('title' => 'Покупка платины')); ?>
<div class="textblock text-xs-center">
	<? if (!empty($message)): ?>
		<p class="message bg-danger"><?=$message ?></p>
	<? endif; ?>

	<p class="message bg-info">
		Для развития проекта Вы можете поддержать нас преобретая платину по следующему курсу:<br><br>1 платина - 1 ОК
	</p>

	<div class="row">
		<div class="col-xs-6">
			<fieldset>
				<legend>Выберите пакет платины</legend>
				<p class="message bg-info">Приобретая кредиты пакетами вы получаете бонус +10% от купленной платины</p>
				<div class="row creditList">
					<div class="col-xs-2"><span class="number">20</span></div>
					<div class="col-xs-2">
						<input type="button" class="btn btn-primary" onclick="buyCredits(20);" value="Купить">
					</div>
				</div>
				<div class="row creditList">
					<div class="col-xs-2"><span class="number">50</span></div>
					<div class="col-xs-2">
						<input type="button" class="btn btn-primary" onclick="buyCredits(50);" value="Купить">
					</div>
				</div>
				<div class="row creditList">
					<div class="col-xs-2"><span class="number">100</span></div>
					<div class="col-xs-2">
						<input type="button" class="btn btn-primary" onclick="buyCredits(100);" value="Купить">
					</div>
				</div>
				<div class="row creditList">
					<div class="col-xs-2"><span class="number">200</span></div>
					<div class="col-xs-2">
						<input type="button" class="btn btn-primary" onclick="buyCredits(200);" value="Купить">
					</div>
				</div>
				<div class="row creditList">
					<div class="col-xs-2"><span class="number">500</span></div>
					<div class="col-xs-2">
						<input type="button" class="btn btn-primary" onclick="buyCredits(500);" value="Купить">
					</div>
				</div>
				<br>

			</fieldset>
		</div>
		<div class="col-xs-6">
			<fieldset>
				<legend>Введите число желаемой платины</legend>
				<div class="form-group">
					<div class="col-xs-5 push-xs-3">
						<input type="text" id="credits" value="10" class="form-control input-sm">
					</div>
					<div class="col-xs-2 push-xs-3">
						<input type="button" class="btn btn-primary" value="Купить" onclick="buyCredits($('#credits').val())">
					</div>
				</div>
			</fieldset>
		</div>
	</div>
</div>

<script type="text/javascript">
	function buyCredits(count)
	{
		if (isNaN(parseInt(count)))
			count = 10;

		showLoading();

		setTimeout(function(){hideLoading();}, 5000);

		if (typeof window.parent.FAPI != 'undefined')
			window.parent.FAPI.UI.showPayment(count+' платина', 'Платина нужны для оплаты дополнительных услуг в игре.', 'credits', parseInt(count), null, '{"userId": <?=$userId ?>}', 'ok', true);
		else if (typeof window.parent.VK != 'undefined')
		{
			window.parent.VK.callMethod('showOrderBox', {type: 'votes', votes: count, item: 'user_<?=$userId ?>'});

			window.parent.VK.addCallback('onOrderSuccess', function (orderId)
			{
				$.toast({
					text: 'На ваш счет зачислена платина. ID платежа: '+orderId,
					icon: 'success'
				});
			});
			window.parent.VK.addCallback('onOrderFail', function (errorCode)
			{
				$.toast({
					text: 'Произошла ошибка при совершении оплаты. errorCode: '+errorCode,
					icon: 'error'
				});
			});
			window.parent.VK.addCallback('onOrderCancel', function ()
			{
				$.toast({
					text: 'Оплата отменена',
					icon: 'info'
				});
			});
		}
		else
			alert('');
	}
</script>