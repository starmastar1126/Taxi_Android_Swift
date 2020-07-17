//
//  CollectPaymentUV.swift
//  DriverApp
//
//  Created by NEW MAC on 29/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class CollectPaymentUV: UIViewController, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var tripFareHLbl: MyLabel!
    @IBOutlet weak var tripFareLbl: MyLabel!
    @IBOutlet weak var tripDateHLbl: MyLabel!
    @IBOutlet weak var tripDateVLbl: MyLabel!
    @IBOutlet weak var discountHLbl: MyLabel!
    @IBOutlet weak var discountVLbl: MyLabel!
    @IBOutlet weak var paymentTypeHLbl: MyLabel!
    @IBOutlet weak var paymentTypeVLbl: MyLabel!
    @IBOutlet weak var generalNoteLbl: MyLabel!
    @IBOutlet weak var generalNoteContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var detailsLbl: UILabel!
    @IBOutlet weak var vehicleTypeLbl: MyLabel!
    @IBOutlet weak var collectPayBtn: MyButton!
    @IBOutlet weak var fareContainerView: UIStackView!
    @IBOutlet weak var fareContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var detailsView: UIView!
    @IBOutlet weak var generalView: UIView!
    @IBOutlet weak var detailsViewHeight: NSLayoutConstraint!
    @IBOutlet weak var vTypeNameHeight: NSLayoutConstraint!
    
    let generalFunc = GeneralFunctions()
    
    var isPageLoaded = false
    
    var window:UIWindow!
    
    var tripData:NSDictionary!
    
    var cntView:UIView!
    
    var loaderView:UIView!
    
    var PAGE_HEIGHT:CGFloat = 445
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
//        self.navigationController?.navigationBar.clipsToBounds = true
        self.navigationController?.navigationBar.layer.zPosition = -1

    }
    
    override func viewWillDisappear(_ animated: Bool) {
//        self.navigationController?.navigationBar.clipsToBounds = false
        self.navigationController?.navigationBar.layer.zPosition = 0
    }
    override func viewDidLoad() {
        super.viewDidLoad()

        window = Application.window!
        
        cntView = self.generalFunc.loadView(nibName: "CollectPaymentScreenDesign", uv: self, contentView: scrollView)
        cntView.backgroundColor = UIColor.clear
        
        scrollView.backgroundColor = UIColor(hex: 0xf2f2f4)
        scrollView.addSubview(cntView)
        
        scrollView.bounces = false
        
        Utils.removeAppInactiveStateNotifications()
    }

    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            
            cntView.frame = CGRect(x: 0, y: 0, width: self.scrollView.frame.size.width, height: self.scrollView.frame.height)

            setData()
            
            isPageLoaded = true
        }
    }
    
    func setData(){
        
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YOUR_TRIP")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YOUR_TRIP")
        
        self.tripFareHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_Total_Fare_TXT").uppercased()
        self.tripDateHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_DATE_TXT").uppercased()
        self.discountHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DIS_APPLIED").uppercased()
        
        self.collectPayBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COLLECT_PAYMENT"))
        self.collectPayBtn.clickDelegate = self
        
        self.detailsLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DETAILS")
        self.detailsLbl.fitText()
        
        self.paymentTypeHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PAYMENT_TYPE_TXT") + ": "
        
        self.headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        generalView.layer.shadowOpacity = 0.5
        generalView.layer.shadowOffset = CGSize(width: 0, height: 3)
        generalView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        Utils.createRoundedView(view: generalView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        detailsView.layer.shadowOpacity = 0.5
        detailsView.layer.shadowOffset = CGSize(width: 0, height: 3)
        detailsView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        Utils.createRoundedView(view: detailsView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        getTripData()
    }

    func getTripData(){
        scrollView.isHidden = true
        loaderView =  self.generalFunc.addMDloader(contentView: self.view)
        loaderView.backgroundColor = UIColor.clear
        
        let parameters = ["type":"displayFare","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.tripFareLbl.text = Configurations.convertNumToAppLocal(numStr: dataDict.getObj(Utils.message_str).get("FareSubTotal"))
                    self.tripDateVLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: dataDict.getObj(Utils.message_str).get("tStartDate"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateWithTime)
                    
                    let fDiscount = dataDict.getObj(Utils.message_str).get("fDiscount")
                    let CurrencySymbol = dataDict.getObj(Utils.message_str).get("CurrencySymbol")
                    let vTripPaymentMode = dataDict.getObj(Utils.message_str).get("vTripPaymentMode")
                    
                    if(dataDict.getObj(Utils.message_str).get("eType").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                        self.vehicleTypeLbl.text = "\(dataDict.getObj(Utils.message_str).get("vVehicleCategory")) - \(dataDict.getObj(Utils.message_str).get("vVehicleType"))"
                    }else{
                        self.vehicleTypeLbl.text = dataDict.getObj(Utils.message_str).get("carTypeName")
                    }

                    let vTypeNameHeight = self.vehicleTypeLbl.text!.height(withConstrainedWidth: Application.screenSize.width - 66, font: UIFont(name: "Roboto-Light", size: 20)!)
                    
                    self.vTypeNameHeight.constant = vTypeNameHeight
                    
                    self.detailsViewHeight.constant = 66 + vTypeNameHeight
                    
                    if (fDiscount != "" && fDiscount != "0" && fDiscount != "0.00") {
                        self.discountVLbl.text = CurrencySymbol + Configurations.convertNumToAppLocal(numStr: fDiscount)
                    }else{
                        self.discountVLbl.text = "--"
                    }
                    
                    if(vTripPaymentMode == "Cash"){
                        self.paymentTypeVLbl.text = self.generalFunc.getLanguageLabel(origValue: "Cash", key: "LBL_CASH_TXT")
                        self.generalNoteLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COLLECT_MONEY_FRM_RIDER")
                    }else{
                        self.paymentTypeVLbl.text = self.generalFunc.getLanguageLabel(origValue: "Card", key: "LBL_CARD_TXT")
                        self.generalNoteLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DEDUCTED_RIDER_CARD")
                    
                    }
                    
                    self.generalNoteContainerViewHeight.constant = 95 + (self.generalNoteLbl.text!).height(withConstrainedWidth: Application.screenSize.width - 46, font: UIFont (name: "Roboto-Light", size: 18)!) - 21.5
                    self.generalNoteLbl.fitText()
                    
                    self.addFareDetails(dataDict: dataDict)
                    
                    self.loaderView.isHidden = true
                    self.scrollView.isHidden = false
                }else{
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                        
                        if(btnClickedId == 0){
                            self.getTripData()
                        }
                    })
                }
                
            }else{
                self.generalFunc.setError(uv: self)
                self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Please try again later", key: "LBL_TRY_AGAIN_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RETRY_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                    
                    if(btnClickedId == 0){
                        self.getTripData()
                    }
                })
            }
            
            
        })
    }
    
    func addFareDetails(dataDict:NSDictionary){
        
        let FareDetailsNewArr = dataDict.getObj(Utils.message_str).getArrObj("FareDetailsNewArr")
    
        for i in 0..<FareDetailsNewArr.count {
            
            let dict_temp = FareDetailsNewArr[i] as! NSDictionary
            
            for (key, value) in dict_temp {
                
                let viewCus = self.generalFunc.loadView(nibName: "FareDataItemView", uv: self, isWithOutSize: true)
                let frame = CGRect(x: 0, y: 0, width: self.fareContainerView.frame.width, height: 40)
                viewCus.frame = frame
                
                let stView = viewCus.subviews[0] as! UIStackView
                
                let lblTitle = stView.subviews[0] as! MyLabel
                let lblValue = stView.subviews[1] as! MyLabel
                
                lblTitle.text = Configurations.convertNumToAppLocal(numStr: key as! String)
                lblValue.text = Configurations.convertNumToAppLocal(numStr: value as! String)
//                print("converted:\(lblTitle.text): \(lblValue.text)")
                self.fareContainerView.addArrangedSubview(viewCus)
                
                if(Configurations.isRTLMode()){
                    lblValue.textAlignment = .left
                }else{
                    lblValue.textAlignment = .right
                }
            }
        }
        
        self.fareContainerViewHeight.constant = CGFloat(40 * FareDetailsNewArr.count)
        
        self.detailsViewHeight.constant = self.detailsViewHeight.constant +  self.fareContainerViewHeight.constant
        
        self.PAGE_HEIGHT = self.PAGE_HEIGHT + self.detailsViewHeight.constant + self.generalNoteContainerViewHeight.constant - 95
        
//        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
            self.cntView.frame.size = CGSize(width: self.contentView.frame.width, height: self.PAGE_HEIGHT)
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)
//        })

    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.collectPayBtn){
            self.collectPayment(isCollectCash: "")
        }
    }
    
    func collectPayment(isCollectCash:String){
        
        let parameters = ["type":"CollectPayment", "iTripId": tripData!.get("TripId"), "isCollectCash": isCollectCash]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else{
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RETRY_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COLLECT_CASH"), completionHandler: { (btnClickedId) in
                        
                        if(btnClickedId == 0){
                            self.collectPayment(isCollectCash: "")
                        }else{
                            self.collectPayment(isCollectCash: "true")
                        }
                    })
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    
    }
}
