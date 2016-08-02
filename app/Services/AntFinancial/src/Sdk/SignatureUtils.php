<?php
namespace AntFinancial\Sdk;

class SignatureUtils
{

    
    /**
     * 根据String获取私钥 - RSA
     *
     * @param encodePrivateKey
     * @return
     * @throws NoSuchAlgorithmException
     * @throws InvalidKeySpecException
     */
    public static function  getPrivateKey($encodePrivateKey){
        
        $Base64 = new \JavaClass('com.sun.org.apache.xerces.internal.impl.dv.util.Base64');
        $privateKeyBytes = $Base64->decode($encodePrivateKey);
        $KeyFactory = new \JavaClass('java.security.KeyFactory');
        $kf = $KeyFactory->getInstance("RSA"); // or "EC" or whatever
        $PKCS8EncodedKeySpec = new \Java('java.security.spec.PKCS8EncodedKeySpec.PKCS8EncodedKeySpec',$privateKeyBytes);
        return $kf->generatePrivate($PKCS8EncodedKeySpec);
    }
    
//     import java.io.ByteArrayOutputStream;
//     import java.security.KeyFactory;
//     import java.security.NoSuchAlgorithmException;
//     import java.security.PrivateKey;
//     import java.security.PublicKey;
//     import java.security.spec.InvalidKeySpecException;
//     import java.security.spec.PKCS8EncodedKeySpec;
//     import java.security.spec.X509EncodedKeySpec;
    
//     import org.apache.commons.io.IOUtils;
//     import org.apache.xml.security.signature.XMLSignature;
//     import org.apache.xml.security.transforms.Transforms;
//     import org.apache.xml.security.utils.Constants;
//     import org.apache.xml.security.utils.XMLUtils;
//     import org.w3c.dom.Document;
//     import org.w3c.dom.Element;
//     import org.w3c.dom.Node;
//     import org.w3c.dom.NodeList;
    
//     import com.sun.org.apache.xerces.internal.impl.dv.util.Base64;
    
    /**
     * XML签名
     *
     * @param priKeyData 私钥数据，PKCS#8编码格式
     * @param xmlDocBytes XML文件内容， UTF-8编码
     * @param elementTagName 续签签名的Tag名称
     * @param algorithm 签名算法 {@link XMLSignature} 支持下列算法
     * <ul>
     * <li>XMLSignature.ALGO_ID_SIGNATURE_RSA</li>
     * <li>XMLSignature.ALGO_ID_SIGNATURE_RSA_SHA1</li>
     * <li>XMLSignature.ALGO_ID_SIGNATURE_RSA_SHA256</li>
     * <li>XMLSignature.ALGO_ID_SIGNATURE_RSA_SHA384</li>
     * <li>XMLSignature.ALGO_ID_SIGNATURE_RSA_SHA512</li>
     * </ul>
     * @param signatureAppendMode 签名节点的附加模式
     * {@link com.alipay.fc.cryptprod.common.service.facade.constant.XmlSignatureAppendMode}
     * <ul>
     * <li>作为子节点： XmlSignatureAppendMode.AS_CHILDREN</li>
     * <li>作为兄弟节点：XmlSignatureAppendMode.AS_BROTHER</li>
     * </ul>
     * @return 签名后的文档 string
     * @throws Exception the exception
     */
    public static function signXmlElement($privateKey, $xmlDocument,
        $elementTagName, $algorithm,
        $signatureAppendMode)  {
        
        $xmlSignature = new \JavaClass('org.apache.xml.security.signature.XMLSignature',$xmlDocument,
            $xmlDocument->getDocumentURI(),$algorithm);
        
        $nodeList = $xmlDocument->etElementsByTagName($elementTagName);
        if ($nodeList == null || $nodeList->getLength() - 1 < 0) {
            throw new \Exception("Document element with tag name " . $elementTagName . " not fount");
        }
    
        $elementNode = $nodeList->item(0);
        if ($elementNode == null) {
            throw new \Exception("Document element with tag name " . $elementTagName . " not fount");
        }
    
        $elementNode->appendChild($xmlSignature->getElement());
        if ($signatureAppendMode == 1) {
            $elementNode->appendChild($xmlSignature->getElement());
        } else if ($signatureAppendMode == 2) {
            $elementNode->getParentNode()->appendChild($xmlSignature->getElement());
        } else {
            throw new \Exception("Illegal Append Mode");
        }
        $transforms = new \Java('org.apache.xml.security.transforms.Transforms',$xmlDocument);
        $transforms->addTransform("http://www.w3.org/2000/09/xmldsig#enveloped-signature");
        $xmlSignature->addDocument("", $transforms, "http://www.w3.org/2000/09/xmldsig#sha1");
    
        $xmlSignature->sign($privateKey);
        $os = new \Java('java.io.ByteArrayOutputStream');
        $XMLUtils = new \JavaClass('org.apache.xml.security.utils.XMLUtils');
        $XMLUtils.outputDOM($xmlDocument, $os);
        return $os->toString("UTF-8").'';
    }
    
    
    
}
