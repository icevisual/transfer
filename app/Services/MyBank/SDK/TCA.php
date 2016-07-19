<?php
namespace App\Services\MyBank\SDK;

class TCA
{

    public static function config($config)
    {
//         ConfigMgr configMgr = ConfigMgr.getInstance();
//         configMgr.init(config);
//         java.util.List keyStoreConfigs = configMgr.getKeyStoreConfigList();
//         KeyStoreMgr.getInstance().init(keyStoreConfigs);
//         java.util.List verifierConfigs = configMgr.getVerifierConfigList();
//         VerifierMgr.getInstance().init(verifierConfigs);
//         String licenseStr = ConfigMgr.getInstance().getLicenseStr();
//         LicenseMgr.getInstance().init(licenseStr);
    }

    public static $digitalSignature = 128;
    public static $nonRepudiation = 64;
    public static $keyEncipherment = 32;
    public static $dataEncipherment = 16;
    public static $keyAgreement = 8;
    public static $keyCertSign = 4;
    public static $cRLSign = 2;
    public static $encipherOnly = 1;
    public static $decipherOnly = 32768;
    public static $contentCommitment = 64;
    public static $SM2 = "SM2";
    public static $RSA1024 = "RSA1024";
    public static $RSA2048 = "RSA2048";
    public static $SM1 = "SM1";
    public static $SM4 = "SM4";
    public static $DES = "DES";
    public static $DESEDE = "3DES";
    public static $DES3 = "3DES";
    public static $AES = "AES";
    public static $SHA1 = "SHA1";
    public static $MD5 = "MD5";
    public static $SM3 = "SM3";
    public static $SHA256 = "SHA256";
    public static $serverAuth = "1.3.6.1.5.5.7.3.1";
    public static $clientAuth = "1.3.6.1.5.5.7.3.2";
    public static $codeSigning = "1.3.6.1.5.5.7.3.3";
    public static $emailProtection = "1.3.6.1.5.5.7.3.4";
    public static $ipsecEndSystem = "1.3.6.1.5.5.7.3.5";
    public static $ipsecTunnel = "1.3.6.1.5.5.7.3.6";
    public static $ipsecUser = "1.3.6.1.5.5.7.3.7";
    public static $timeStamping = "1.3.6.1.5.5.7.3.8";
    public static $OCSPSigning = "1.3.6.1.5.5.7.3.9";
    public static $dvcs = "1.3.6.1.5.5.7.3.10";
    public static $sbgpCertAAServerAuth = "1.3.6.1.5.5.7.3.11";
    public static $scvpResponder = "1.3.6.1.5.5.7.3.12";
    public static $eapOverPPP = "1.3.6.1.5.5.7.3.13";
    public static $eapOverLAN = "1.3.6.1.5.5.7.3.14";
    public static $scvpServer = "1.3.6.1.5.5.7.3.15";
    public static $scvpClient = "1.3.6.1.5.5.7.3.16";
    public static $ipsecIKE = "1.3.6.1.5.5.7.3.17";
    public static $capwapAC = "1.3.6.1.5.5.7.3.18";
    public static $capwapWTP = "1.3.6.1.5.5.7.3.19";
    public static $smartcardlogon = "1.3.6.1.4.1.311.20.2.2";
    public static $softVersion = "3.1.0.0";
    public static $licenseVersion = "1.0.0.0";

//     static
//     {
//         Security.addProvider(TopSMProvider.INSTANCE);
//         Security.addProvider(new BouncyCastleProvider());
//     }

    
}


