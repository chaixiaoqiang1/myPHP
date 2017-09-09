<?php

class GameServerApiTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */

	public function testConnect()
	{
		$this->assertEquals($this->getApi()->url, 'http://192.168.0.186:3328/2.server');
	}

	private function getApi()
	{
		$api = GameServerApi::connect('192.168.0.186', '3328', '2');
		return $api;
	}	

	public function testIsCreatedPlayer()
	{
		$response = $this->getApi()->isCreatedPlayer(1372618226);
		if (is_object($response) && $response->player_id) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}

	//每次都需要重置第一个参数
	public function testRecharge()
	{
		$response = $this->getApi()->recharge('1112112', 1372618226, 30);
		if (is_object($response) && $response->new_recharge) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}

	
	public function testOnlinePlayerNumber()
	{
		$response = $this->getApi()->getOnlinePlayersNumber();	
		if (is_object($response) && $response->num_online) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}

	public function testPlayerList()
	{
		$response = $this->getApi()->getOnlinePlayersList();
		if (is_object($response) && $response->receivers) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}
	
	public function testFreezeAccount()
	{
		$response = $this->getApi()->freezeAccount(67371105, 1);
		$this->assertEquals('OK', $response->result);
	}
	
	public function testBanChat()
	{
		$response = $this->getApi()->banChat(67371105, 1);
		$this->assertEquals('OK', $response->result);
	}

	public function testAnnouce()
	{
		$response = $this->getApi()->announce(1, 1, 1, 60, 'Test Api');
		$this->assertEquals('OK', $response->result);
	}

	public function testCreateGiftCode()
	{
		$response = $this->getApi()->createGiftCode(1, 1);
		$this->assertEquals(1, $response->code_type);
	}
	
	public function testAddWordFilter()
	{
		$response = $this->getApi()->addWordFilter(['干了啊']);
		$this->assertEquals('OK', $response->result);
	}

	public function testSendGiftBagToPlayers()
	{
		$response = $this->getApi()->sendGiftBagToPlayers(30300048, [67371105]);
		$this->assertEquals('OK', $response->result);
	}
	
	public function testHitGoldenEgg()
	{
		$response = $this->getApi()->HitGoldenEgg(1);
		$this->assertEquals('OK', $response->result);
	}

	public function testGMQuestion()
	{
		$response = $this->getApi()->getGMQuestions();
		if (is_object($response) && $response->GM_Logs) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}

	public function testReplyGMQuestion()
	{
		$response = $this->getApi()->replyGMQuestion(5, 67371105, 3, 'Test Api');	
		$this->assertEquals('OK', $response->result);
	}

	public function testPlayerIDByName()
	{
		$name = '柯第丘';
		$response = $this->getApi()->getPlayerInfoByName($name);
		$this->assertEquals($name, $response->name);
	}

	public function testGiftCodeStatusByCode()
	{
		$code = '0024a722-6538-4998-8c8f-5091df595010';
		$response = $this->getApi()->getGiftCodeStatusByCode($code);
		$this->assertEquals($code, $response[0]->Code);
	}

	public function testGiftCodeStatusByCodeType()
	{
		$code_type = 1;
		$response = $this->getApi()->getGiftCodeStatusByCodeType($code_type);
		if (is_array($response) && count($response) > 0) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}

	public function testGiftCodeStatusByPlayerID()
	{
		$player_id = 67371105;
		$response = $this->getApi()->getGiftCodeStatusByPlayerID($player_id);
		$this->assertEquals('OK', $response->result);
	}

	public function testPlayerInfoByPlayerID()
	{
		$player_id = 67371105;
		$response = $this->getApi()->getPlayerInfoByPlayerID($player_id);
		$this->assertEquals(0, $response->IsGM);
	}
	
	public function testCreateGiftBagForAllServer()
	{
		$gift_bag_id = 30300048;
		$response = $this->getApi()->createGiftBagForAllServer($gift_bag_id, 1, 'Test Api');

		$this->assertEquals('OK', $response->result);
	}

	public function testAddOrDeleteTitle()
	{
		$response = $this->getApi()->addOrDeleteTitle(67371105, 1);
		$response->none;
	}

	public function testChangeYuanbao()
	{
		$response = $this->getApi()->changeYuanbao(67371105, 100);
		$this->assertEquals('OK', $response->result);
	}

	public function testAddItemToBackpack()
	{
		$response = $this->getApi()->addItemToBackpack(30300048, 67371105);
		$this->assertEquals('OK', $response->result);
	}

	public function testGetExchangePromotion()
	{
		$response = $this->getApi()->getExchangePromotion();
		if (is_object($response) && $response->close_time) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}	
	}

	public function testAddExchangePromotion()
	{
		$open_time = date('Y-n-j\TG:i:s', time() - 3600);
		$close_time = date('Y-n-j\TG:i:s', time() + 3600);
		$response = $this->getApi()->addExchangePromotion($open_time, $close_time);
		if ($response->error_code) {
			$this->assertTrue(false);
		}
	}

	public function testCloseExchangePromotion()
	{
		$response = $this->getApi()->closeExchangePromotion();
		$this->assertEquals('OK', $response->result);
	}

	public function testGetPromotion()
	{
		$response = $this->getApi()->getPromotion();
		if (is_object($response) && $response->activities) {
			$this->assertTrue(true);
		} else {
			$this->assertTrue(false);
		}
	}

	public function testAddPromotion()
	{
		$open_time = date('Y-n-j\TG:i:s', time() + 100);
		$close_time = date('Y-n-j\TG:i:s', time() + 3600*2);
		var_dump($open_time, $close_time);
		$response = $this->getApi()->addPromotion(1, $open_time, $close_time);
		if ($response->error_code) {
			$this->assertTrue(false);
		}
	}

	public function testClosePromotion()
	{
		$response = $this->getApi()->closePromotion(1);
		$this->assertEquals('OK', $response->result);
	}
	
	public function testSendMail()
	{
		$response = $this->getApi()->sendMail(67371105, 'Test Api', 'Test Api');
		$this->assertEquals('OK', $response->result);
	}

	public function testAddOrDeleteZuoQi()
	{
		$response = $this->getApi()->addOrDeleteZuoQi(67371105, 1);
		if ($response->error_code) {
			$this->assertTrue(false);
		}
	}

	public function testSetWeather()
	{
		$response = $this->getApi()->setWeather(1);
		$this->assertEquals('OK', $response->result);
	}
	
	public function testSendGiftBagToUser()
	{
		$response = $this->getApi()->sendGiftBagToUser(30300048, 1372618226);
		$this->assertEquals('OK', $response->result);
	}

}