//
//  PaymentUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 19/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class PaymentUV: UIViewController, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var headerLbl: MyLabel!
    @IBOutlet weak var subHeaderLbl: MyLabel!
    @IBOutlet weak var configCardBtn: MyButton!
    @IBOutlet weak var cardNumTxtField: MyTextField!
    @IBOutlet weak var demoView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    
    // SITE_TYPE = Demo
    @IBOutlet weak var notesLbl: MyLabel!
    @IBOutlet weak var notesDescLbl: UILabel!
    @IBOutlet weak var cardTypeHLbl: UILabel!
    @IBOutlet weak var cardTypeVLbl: UILabel!
    @IBOutlet weak var cardNumLbl: UILabel!
    @IBOutlet weak var cardNumVLbl: UILabel!
    @IBOutlet weak var expiryLbl: UILabel!
    @IBOutlet weak var expiryVLbl: UILabel!
    @IBOutlet weak var cvvLbl: UILabel!
    @IBOutlet weak var cvvVlbl: UILabel!
    @IBOutlet weak var cardDetailAreaView: UIView!
    @IBOutlet weak var cardDetailAreaViewHeight: NSLayoutConstraint!
    @IBOutlet weak var demoHintCarddetailView: UIView!
    
    var isFromMainScreen = false
    
    let generalFunc = GeneralFunctions()
    
    var cntView:UIView!
    
    var PAGE_HEIGHT:CGFloat = 600
    
    var isFirstLaunch = true
    
    var isFromUFXPayMode = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
//        self.navigationController?.navigationBar.layer.zPosition = -1
        
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        self.navigationController?.navigationBar.layer.zPosition = 1
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        cntView = self.generalFunc.loadView(nibName: "PaymentScreenDesign", uv: self, contentView: scrollView)
        self.scrollView.backgroundColor = UIColor(hex: 0xF2F2F4)
        self.scrollView.addSubview(cntView)
        
//        self.contentView.addSubview(self.generalFunc.loadView(nibName: "PaymentScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
    }
    
    
    override func viewDidAppear(_ animated: Bool) {
        self.navigationController?.navigationBar.layer.zPosition = -1
        
        if(isFirstLaunch){
            
            self.scrollView.bounces = false
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
            
            isFirstLaunch = false
            
//            setData()
        }
    }
    
    func setData(){
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CARD_PAYMENT_DETAILS")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CARD_PAYMENT_DETAILS")
        
        
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        headerView.layer.shadowOpacity = 0.5
        //        headerView.layer.shadowRadius = 1.1
        headerView.layer.shadowOffset = CGSize(width: 0, height: 3)
        headerView.layer.shadowColor = UIColor(hex: 0xc0c0c1).cgColor
        headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        let vCreditCard = userProfileJson.get("vCreditCard")
        let vStripeCusId = userProfileJson.get("vStripeCusId")
        
        if(vStripeCusId == ""){
            headerLbl.text = self.generalFunc.getLanguageLabel(origValue: "No Card Available", key: "LBL_NO_CARD_AVAIL_HEADER_NOTE")
            configCardBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_CARD"))
            subHeaderLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_CARD_AVAIL_NOTE")
            self.cardNumTxtField.isHidden = true
        }else{
            headerLbl.isHidden = true
            subHeaderLbl.isHidden = true
            configCardBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHANGE"))
            
            self.cardNumTxtField.isHidden = false
            
            self.cardNumTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CARD_NUMBER_HEADER_TXT"))
            self.cardNumTxtField.setText(text: vCreditCard)
            self.cardNumTxtField.setEnable(isEnabled: false)
            
        }
        
        headerLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        subHeaderLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        configCardBtn.clickDelegate = self
        
        cardDetailAreaView.layer.shadowOpacity = 0.5
        cardDetailAreaView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cardDetailAreaView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        if(userProfileJson.get("SITE_TYPE").uppercased() == "DEMO"){
            cardDetailAreaView.isHidden = false
            
            
            self.notesDescLbl.text = self.generalFunc.getLanguageLabel(origValue: "Since this is the demo version, please use below dummy credit/debit card for testing. The actual payment will not be deducted.", key: "LBL_DEMO_CARD_DESC")
            
            
            let noteHeight = self.generalFunc.getLanguageLabel(origValue: "Since this is the demo version, please use below dummy credit/debit card for testing. The actual payment will not be deducted.", key: "LBL_DEMO_CARD_DESC").height(withConstrainedWidth: Application.screenSize.width - 25, font: UIFont(name: "Roboto-Light", size: 16)!)
            
            PAGE_HEIGHT = PAGE_HEIGHT + noteHeight
        }else{
            cardDetailAreaView.isHidden = true
            
            PAGE_HEIGHT = PAGE_HEIGHT - self.cardDetailAreaViewHeight.constant
            
            
            self.notesDescLbl.text = self.generalFunc.getLanguageLabel(origValue: "Your card information is secured with our payment gateway. All transactions are performed under the standard security and all performed transactions are confidential. Your information will not be shared to third party.", key: "LBL_CARD_INFO_SECURE_NOTE")
            
            
            let noteHeight = self.generalFunc.getLanguageLabel(origValue: "Your card information is secured with our payment gateway. All transactions are performed under the standard security and all performed transactions are confidential. Your information will not be shared to third party.", key: "LBL_CARD_INFO_SECURE_NOTE").height(withConstrainedWidth: Application.screenSize.width - 25, font: UIFont(name: "Roboto-Light", size: 16)!)
            
            PAGE_HEIGHT = PAGE_HEIGHT + noteHeight
            
        }
        
        
        self.notesLbl.text = self.generalFunc.getLanguageLabel(origValue: "NOTES", key: "LBL_NOTES")
        
        self.notesDescLbl.fitText()
        
        self.cardTypeHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Card Type", key: "LBL_CARD_TYPE") + ":"
        self.cardNumLbl.text = self.generalFunc.getLanguageLabel(origValue: "Card Number", key: "LBL_CARD_NUMBER_TXT") + ":"
        self.expiryLbl.text = self.generalFunc.getLanguageLabel(origValue: "Expiry", key: "LBL_EXPIRY") + ":"
        self.cvvLbl.text = self.generalFunc.getLanguageLabel(origValue: "CVV", key: "LBL_CVV") + ":"
        
        self.cardNumVLbl.text = "4111 1111 1111 1111"
        self.expiryVLbl.text = "12/2023"
        self.cvvVlbl.text = "123"
        self.cardTypeVLbl.text = "VISA"
        
        STPPaymentConfiguration.shared().publishableKey = userProfileJson.get("STRIPE_PUBLISH_KEY")
        
    }

    func myBtnTapped(sender: MyButton) {
        if(sender == self.configCardBtn){
//            let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
//
//            let vTripStatus = userProfileJson.get("vTripStatus")
            
//            if(vTripStatus != "Not Active" && vTripStatus != "NONE" && vTripStatus != "Not Requesting" && vTripStatus != "Cancelled"){
//                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DIS_ALLOW_EDIT_CARD"))
//                
//                return
//            }
            
//            let addPaymentUv = GeneralFunctions.instantiateViewController(pageName: "AddPaymentUV") as! AddPaymentUV
//            addPaymentUv.PAGE_MODE = self.cardNumTxtField.isHidden ? "ADD" : "EDIT"
//            addPaymentUv.paymentUv = self
//            
//            self.pushToNavController(uv: addPaymentUv)
            
            checkUserStatus()
        }
    }
    
    func checkUserStatus(){
        let parameters = ["type":"checkUserStatus","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let addPaymentUv = GeneralFunctions.instantiateViewController(pageName: "AddPaymentUV") as! AddPaymentUV
                    addPaymentUv.PAGE_MODE = self.cardNumTxtField.isHidden ? "ADD" : "EDIT"
                    addPaymentUv.isFromUFXPayMode = self.isFromUFXPayMode
                    addPaymentUv.paymentUv = self
                    addPaymentUv.isFromMainScreen = self.isFromMainScreen
                    
                    self.pushToNavController(uv: addPaymentUv)
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }

}
