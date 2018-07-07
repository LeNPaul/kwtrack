<?php
namespace AmazonAdvertisingApi;

require_once "/AmazonAdvertisingApi/Client.php";

class ClientTest
{
    private $client = null;
    private $return_value = null;
    private $config = array(
        "clientId" => "amzn1.application-oa2-client.4246e0f086e441259742c758f63ca0bf",
        "clientSecret" => "9c9e07b214926479e14a0781051ecc3ad9b29686d3cef24e15eb130a47cabeb3",
        "region" => "na",
        "accessToken" => "Atza|IwEBIOQy4ODPZ2w67eTShuUfNglRSIVWGWe8-gx05OiMTmNcuvMkP4yn182uw2w1-fegqmcLblgjPiKknA2JIdOyyPFwCOm5lUyKbZXneGl2J_VrehDyahZUuKZhUKQCSyHS-oQyc93LzNB36bvCyOKeNRyQp1RGt7JQbRO-tFdBfFsXZTxH6jPCOuSn9t-rFJC9wPQj6vpNczKfXU1eiDvUJ0Mi_JeRyufYcnCU7726N3k4EtEAuKzRTrepCaojH6NB3VTKQCTg8Ft7vXgKdUCtgOwaOIgoM9ecbwivf6G3B3f3S86N3Xh4zdehZJOwbetW7l7R6Vmz6zdwz2I72FAIDaxVh66H7bL2gHvzFgFU-5UjJU0skZyEy4cKkttc0KHx1wdQ_hfaGLnyDjFnuelS7MN3QrubCLY2WZZ_ipgIBihdnp3Ctafl3N50ZLxr63FxvTY-kvLxAB4Dg-7ME6eOkzkcYJpZNbTYoV02nXobjAfHiGMt-oHIJkZkSaYq8wfd48yTaqffUsG1n7Xz_6mFnzHPN2P8SEBkA7SNuVpf_jjcdr_dr_Ma_2AXUk23BtfD-JUTbjPnR_wj20dCIdog6nCL",
        "refreshToken" => "Atzr|IwEBIP9tInWp1t__Vk1JZDUHyuWUu2rbgbkL339xqXIJ9rp_x4aVgx-vyHQpi7VxwnOoTRCP-3X6dEHlyfniD5Fs9oWfeiiLvfyIhTOkx9-blulp3U2EgUNx7Z0EuiQxgk9fS1n5t0ELLaq1kSZ9Ja_1xl1Ec7r_XwxO40IB8ipt5RdE-fAnu6PKbwj6eR4XLdk1t2DqxSWGRhoVEG715WX2Y1Hzsi8LgdloR47APPAwg_BO1qdikz2M_zkgXQtmbBV46v55u8vzflAq-DYdGgOxwKtmU6U2cJKSwZqwmI2uwebT_2JCWH-xHjCxAlLzloNBqt3CoknPsFVmuQAtE1ElzUPqUXUkd3n0XPpIsbWtI6plKSQq2steikD7CwUMfP0WLAhA2vIfQF1cbrBQ6iWSd6pO8LZyChfB4FyBGwzHGr11wuTMAvZ7KhKNVxoD6VaTi9uADzLur8QoTt7C0ekZzGnW2N8G2p4nA8g0izvBTK6EBnHuCozD5YJXy965-4h3rZcmTqXu8Lv0Q5OzY3xHRfwQN-xVTgVUUFLPGZc4ojC_uZupXTQsi1ZmIyp83nvNwT4",
        "sandbox" => true);


    public function setUp()
    {
        $this->return_value = array(
            "code" => "200",
            "success" => true,
            "requestId" => "test",
            "response" => "SUCCESS");

        $this->client = $this->getMockBuilder("AmazonAdvertisingApi\Client")
                             ->setConstructorArgs(array($this->config))
                             ->setMethods(array("_executeRequest"))
                             ->getMock();

        $this->client->expects($this->any())
             ->method("_executeRequest")
             ->will($this->returnValue($this->return_value));
    }


    public function testValidateClientId()
    {
        $testConfig = $this->config;
        $testConfig["clientId"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertRegExp("/Invalid parameter value for clientId./", strval($expected));
        }
    }

    public function testValidateClientSecret()
    {
        $testConfig = $this->config;
        $testConfig["clientSecret"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertRegExp("/Invalid parameter value for clientSecret./", strval($expected));
        }
    }

    public function testValidateRegion()
    {
        $testConfig = $this->config;
        $testConfig["region"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertRegExp("/Invalid region./", strval($expected));
        }
    }

    public function testValidateAccessToken()
    {
        $testConfig = $this->config;
        $testConfig["accessToken"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertRegExp("/Invalid parameter value for accessToken./", strval($expected));
        }
    }

    public function testValidateRefreshToken()
    {
        $testConfig = $this->config;
        $testConfig["refreshToken"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertRegExp("/Invalid parameter value for refreshToken./", strval($expected));
        }
    }

    public function testValidateSandbox()
    {
        $testConfig = $this->config;
        $testConfig["sandbox"] = "bad";
        try {
            $client = new Client($testConfig);
        } catch (\Exception $expected) {
            $this->assertRegExp("/Invalid parameter value for sandbox./", strval($expected));
        }
    }

    public function testListProfiles()
    {
        $request = $this->client->listProfiles();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetProfile()
    {
        $request = $this->client->getProfile("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateProfiles()
    {
        $request = $this->client->updateProfiles("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetCampaign()
    {
        $request = $this->client->getCampaign("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetCampaignEx()
    {
        $request = $this->client->getCampaignEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateCampaigns()
    {
        $request = $this->client->createCampaigns("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveCampaign()
    {
        $request = $this->client->archiveCampaign("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListCampaigns()
    {
        $request = $this->client->listCampaigns();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListCampaignsEx()
    {
        $request = $this->client->listCampaignsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetAdGroup()
    {
        $request = $this->client->getAdGroup("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetAdGroupEx()
    {
        $request = $this->client->getAdGroupEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateAdGroups()
    {
        $request = $this->client->createAdGroups("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateAdGroups()
    {
        $request = $this->client->updateAdGroups("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveAdGroup()
    {
        $request = $this->client->archiveAdGroup("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListAdGroups()
    {
        $request = $this->client->listAdGroups();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListAdGroupsEx()
    {
        $request = $this->client->listAdGroupsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetBiddableKeyword()
    {
        $request = $this->client->getBiddableKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetBiddableKeywordEx()
    {
        $request = $this->client->getBiddableKeywordEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateBiddableKeywords()
    {
        $request = $this->client->createBiddableKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function updateCreateBiddableKeywords()
    {
        $request = $this->client->updateBiddableKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveBiddableKeyword()
    {
        $request = $this->client->archiveBiddableKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListBiddableKeywords()
    {
        $request = $this->client->listBiddableKeywords();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListBiddableKeywordsEx()
    {
        $request = $this->client->listBiddableKeywordsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetNegativeKeyword()
    {
        $request = $this->client->getNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetNegativeKeywordEx()
    {
        $request = $this->client->getNegativeKeywordEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateNegativeKeywords()
    {
        $request = $this->client->createNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateNegativeKeywords()
    {
        $request = $this->client->updateNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveNegativeKeyword()
    {
        $request = $this->client->archiveNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListNegativeKeywords()
    {
        $request = $this->client->listNegativeKeywords();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListNegativeKeywordsEx()
    {
        $request = $this->client->listNegativeKeywordsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetCampaignNegativeKeyword()
    {
        $request = $this->client->getCampaignNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetCampaignNegativeKeywordEx()
    {
        $request = $this->client->getCampaignNegativeKeywordEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateCampaignNegativeKeywords()
    {
        $request = $this->client->createCampaignNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateCampaignNegativeKeywords()
    {
        $request = $this->client->updateCampaignNegativeKeywords("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testRemoveCampaignNegativeKeyword()
    {
        $request = $this->client->removeCampaignNegativeKeyword("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListCampaignNegativeKeywords()
    {
        $request = $this->client->listCampaignNegativeKeywords();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListCampaignNegativeKeywordsEx()
    {
        $request = $this->client->listCampaignNegativeKeywordsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetProductAd()
    {
        $request = $this->client->getProductAd("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetProductAdEx()
    {
        $request = $this->client->getProductAdEx("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testCreateProductAds()
    {
        $request = $this->client->createProductAds("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testUpdateProductAds()
    {
        $request = $this->client->updateProductAds("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testArchiveProductAd()
    {
        $request = $this->client->archiveProductAd("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testListProductAds()
    {
        $request = $this->client->listProductAds();
        $this->assertEquals($this->return_value, $request);
    }

    public function testListProductAdsEx()
    {
        $request = $this->client->listProductAdsEx();
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetAdGroupBidRecommendations()
    {
        $request = $this->client->getAdGroupBidRecommendations("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetKeywordBidRecommendations()
    {
        $request = $this->client->getKeywordBidRecommendations("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testBulkGetKeywordBidRecommendations()
    {
        $request = $this->client->getKeywordBidRecommendations("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetAdGroupKeywordSuggestions()
    {
        $request = $this->client->getAdGroupKeywordSuggestions(
            array("adGroupId" => 12345));
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetAdGroupKeywordSuggestionsEx()
    {
        $request = $this->client->getAdGroupKeywordSuggestionsEx(
            array("adGroupId" => 12345));
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetAsinKeywordSuggestions()
    {
        $request = $this->client->getAsinKeywordSuggestions(
            array("asin" => 12345));
        $this->assertEquals($this->return_value, $request);
    }

    public function testBulkGetAsinKeywordSuggestions()
    {
        $request = $this->client->bulkGetAsinKeywordSuggestions(
            array("asins" => array("ASIN1", "ASIN2")));
        $this->assertEquals($this->return_value, $request);
    }

    public function testRequestSnapshot()
    {
        $request = $this->client->requestSnapshot("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetSnapshot()
    {
        $request = $this->client->getSnapshot("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testRequestReport()
    {
        $request = $this->client->requestReport("test");
        $this->assertEquals($this->return_value, $request);
    }

    public function testGetReport()
    {
        $request = $this->client->getReport("test");
        $this->assertEquals($this->return_value, $request);
    }
}

$client = new ClientTest();

$client->testGetReport();