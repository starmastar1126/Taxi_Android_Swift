//
//  RatingUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 01/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class RatingUV: UIViewController, MyBtnClickDelegate, MyLabelClickDelegate {
    
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var totalFareHLbl: MyLabel!
    @IBOutlet weak var totalFareVLbl: MyLabel!
    @IBOutlet weak var billDateHLbl: MyLabel!
    @IBOutlet weak var billDateVLbl: MyLabel!
    @IBOutlet weak var dashedView: UIView!
//    @IBOutlet weak var billDateVlblXposition: NSLayoutConstraint!
//    @IBOutlet weak var billDateVlblWidth: NSLayoutConstraint!
    @IBOutlet weak var tripGeneralInfoLbl: MyLabel!
    @IBOutlet weak var discountHLbl: MyLabel!
    @IBOutlet weak var discountVLbl: MyLabel!
    @IBOutlet weak var sourceAddressLbl: MyLabel!
    @IBOutlet weak var destAddLbl: MyLabel!
    @IBOutlet weak var howWasLbl: MyLabel!
    @IBOutlet weak var ratingBar: RatingView!
    @IBOutlet weak var commentTxtView: KMPlaceholderTextView!
    @IBOutlet weak var submitBtn: MyButton!
    @IBOutlet weak var addressContainerView: UIView!
    @IBOutlet weak var addressContainerViewTopMargin: NSLayoutConstraint!
    @IBOutlet weak var addressContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var rateContainerView: UIView!
    @IBOutlet weak var endLocPinImgView: UIImageView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var addressContainerCenterViewYPosition: NSLayoutConstraint!
    @IBOutlet weak var addressContainerSeperatorView: UIView!
    @IBOutlet weak var vTypeLbl: MyLabel!
    @IBOutlet weak var detailsViewHeight: NSLayoutConstraint!
    @IBOutlet weak var detailsArrowImgView: UIImageView!
    @IBOutlet weak var detailsLBl: MyLabel!
    @IBOutlet weak var fareDataContainerStkView: UIStackView!
    @IBOutlet weak var fareContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var detailsView: UIView!
    @IBOutlet weak var detailsClickView: UIView!
    
    // Give Tip OutLets
    @IBOutlet weak var tipHImgView: UIImageView!
    @IBOutlet weak var giveTipHLbl: MyLabel!
    @IBOutlet weak var giveTipNoteLbl: MyLabel!
    @IBOutlet weak var giveTipPLbl: MyLabel!
    @IBOutlet weak var giveTipNLbl: MyLabel!
    @IBOutlet weak var enterTipTxtField: MyTextField!
    
    var currentTipMode = "OFF"
    
    let generalFunc = GeneralFunctions()
    
    var loaderView:UIView!
    
    var isPageLoad = false
    var window:UIWindow!
    
    var iTripId = ""
    
    var cntView:UIView!
    
    var PAGE_HEIGHT:CGFloat = 710
    var ENABLE_TIP_MODULE = ""
    
    var giveTipView:UIView!
    var bgTipView:UIView!
    
    var isBottomViewSet = false
    
    var tripFinishView:UIView!
    var tripFinishBGView:UIView!
    
    var isSafeAreaSet = false
    
    var fareContainerViewHeightTemp:CGFloat = 0
    var detailsViewHeightTemp:CGFloat = 0
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        self.navigationController?.navigationBar.layer.zPosition = -1

    }
    
    override func viewWillDisappear(_ animated: Bool) {
        //        self.navigationController?.navigationBar.clipsToBounds = false
        self.navigationController?.navigationBar.layer.zPosition = 0
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
         window = Application.window!
        
        cntView = self.generalFunc.loadView(nibName: "RatingScreenDesign", uv: self, contentView: scrollView)
        cntView.backgroundColor = UIColor.clear
        self.scrollView.addSubview(cntView)
        self.scrollView.isHidden = true
        
        scrollView.bounces = false
        scrollView.backgroundColor = UIColor.clear
        
        if(PAGE_HEIGHT < self.contentView.frame.height){
            PAGE_HEIGHT = self.contentView.frame.height
        }
        
        if(iTripId != ""){
            self.addBackBarBtn()
        }
        
        setData()
    }
    
    override func viewDidLayoutSubviews() {
        
        if(isSafeAreaSet == false){
            
            if(cntView != nil){
                scrollView.frame.size.height = scrollView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            }
            
            isSafeAreaSet = true
        }
    }
    
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoad == false){
            
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT)
            
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT)
            
            getTripData()
            
            isPageLoad = true
        }
        UIApplication.shared.isStatusBarHidden = false
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RATING")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RATING")
        
        addressContainerView.layer.shadowOpacity = 0.5
        addressContainerView.layer.shadowOffset = CGSize(width: 0, height: 6)
        addressContainerView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        rateContainerView.layer.shadowOpacity = 0.5
        rateContainerView.layer.shadowOffset = CGSize(width: 0, height: 6)
        rateContainerView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        tripGeneralInfoLbl.layer.shadowOpacity = 0.5
        tripGeneralInfoLbl.layer.shadowOffset = CGSize(width: 0, height: 6)
        tripGeneralInfoLbl.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        self.submitBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_SUBMIT_TXT"))
        
        self.totalFareHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_Total_Fare_TXT").uppercased()
        self.billDateHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_DATE_TXT").uppercased()
        self.discountHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DIS_APPLIED").uppercased()
        
        
        self.detailsLBl.text = self.generalFunc.getLanguageLabel(origValue: "Fare details", key: "LBL_FARE_DETAILS")
        self.commentTxtView.placeholder = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WRITE_COMMENT_HINT_TXT")
        
        self.submitBtn.clickDelegate = self
        
        detailsArrowImgView.transform = CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180))
        
        GeneralFunctions.setImgTintColor(imgView: detailsArrowImgView, color: UIColor(hex: 0x333333))
        
        let detailsViewTapGue = UITapGestureRecognizer()
        detailsViewTapGue.addTarget(self, action: #selector(self.detailsViewTapped))
        
        detailsClickView.isUserInteractionEnabled = true
        detailsClickView.addGestureRecognizer(detailsViewTapGue)
        
        Utils.createRoundedView(view: detailsView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        Utils.createRoundedView(view: tripGeneralInfoLbl, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        Utils.createRoundedView(view: addressContainerView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        Utils.createRoundedView(view: rateContainerView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)

        GeneralFunctions.setImgTintColor(imgView: self.endLocPinImgView, color: UIColor(hex: 0xFF0000))
        
        self.headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
    }
    
    func detailsViewTapped(){
        if(self.detailsViewHeight.constant > 60){
            
            UIView.animate( withDuration: 0.25, delay: 0, options: .curveEaseInOut, animations: {
                    self.detailsArrowImgView.transform = CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180))
                    self.fareContainerViewHeight.constant = 0
                    self.detailsViewHeight.constant = 55
                    self.cntView.layoutIfNeeded()
                    self.view.layoutIfNeeded()
                    self.PAGE_HEIGHT = self.PAGE_HEIGHT - self.fareContainerViewHeightTemp
                    
                    self.cntView.frame.size = CGSize(width: self.cntView.frame.width, height: self.PAGE_HEIGHT)
                    
                    self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)
            })
            
        }else{
            
            UIView.animate( withDuration: 0.25, delay: 0, options: .curveEaseInOut, animations: {
                    self.detailsArrowImgView.transform = CGAffineTransform(rotationAngle: -90 * CGFloat(CGFloat.pi/180))
                
                    self.fareContainerViewHeight.constant = self.fareContainerViewHeightTemp
                    self.detailsViewHeight.constant = self.detailsViewHeightTemp
                    self.cntView.layoutIfNeeded()
                    self.view.layoutIfNeeded()
                
                    self.PAGE_HEIGHT = self.PAGE_HEIGHT + self.fareContainerViewHeightTemp
                
                    self.cntView.frame.size = CGSize(width: self.cntView.frame.width, height: self.PAGE_HEIGHT)
                
                    self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)
            })
            
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.submitBtn){
            
            if(self.ratingBar.rating > 0.0){
                
                //            submitRating()
                if(self.ENABLE_TIP_MODULE == "Yes"){
                    self.loadTipView()
                }else{
                    submitRating(fAmount: "", isCollectTip: "No")
                }
                
            }else{
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ERROR_RATING_DIALOG_TXT"), uv: self)
            }
            
        }
    }
    
    func getTripData(){
        scrollView.isHidden = true
        loaderView =  self.generalFunc.addMDloader(contentView: self.view)
        loaderView.backgroundColor = UIColor.clear
        
        let parameters = ["type":"displayFare","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iTripId": iTripId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    if Utils.cabGeneralType_Deliver.uppercased() == dataDict.get("eType").uppercased()
                    {
                        self.howWasLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOW_WAS_YOUR_DELIVERY")
                    }
                    else
                    {
                        self.howWasLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_HOW_WAS_RIDE")
                    }
                    self.totalFareVLbl.text = Configurations.convertNumToAppLocal(numStr: dataDict.getObj(Utils.message_str).get("FareSubTotal"))
                    self.billDateVLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: dataDict.getObj(Utils.message_str).get("tStartDate"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateWithTime)
                    
                    let fDiscount = dataDict.getObj(Utils.message_str).get("fDiscount")
                    let CurrencySymbol = dataDict.getObj(Utils.message_str).get("CurrencySymbol")
                    _ = dataDict.getObj(Utils.message_str).get("vTripPaymentMode")
                    
                    let eCancelled = dataDict.getObj(Utils.message_str).get("eCancelled")
                    let vCancelReason = dataDict.getObj(Utils.message_str).get("vCancelReason")
//                    self.vehicleTypeLbl.text = dataDict.getObj(Utils.message_str).get("carTypeName")
                    
                    if (fDiscount != "" && fDiscount != "0" && fDiscount != "0.00") {
                        self.discountVLbl.text = CurrencySymbol + Configurations.convertNumToAppLocal(numStr: fDiscount)
                    }else{
                        self.discountVLbl.text = "--"
                    }
                    
                    if(dataDict.getObj(Utils.message_str).get("eType").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
                        self.vTypeLbl.text = "\(dataDict.getObj(Utils.message_str).get("vVehicleCategory"))-\(dataDict.getObj(Utils.message_str).get("vVehicleType"))"
                    }else{
                        self.vTypeLbl.text = dataDict.getObj(Utils.message_str).get("carTypeName")
                    }
                    
                    self.vTypeLbl.fitText()
                    
                    let vTypeNameHeight = self.vTypeLbl.text!.height(withConstrainedWidth: Application.screenSize.width - 20, font: UIFont(name: "Roboto-Light", size: 18)!)

                    self.PAGE_HEIGHT = self.PAGE_HEIGHT + vTypeNameHeight
                    
                    self.ENABLE_TIP_MODULE = dataDict.getObj(Utils.message_str).get("ENABLE_TIP_MODULE")
                    
                    let tSaddress = dataDict.getObj(Utils.message_str).get("tSaddress").trim()
                    let tDAddress = dataDict.getObj(Utils.message_str).get("tDaddress").trim()
                    
                    self.sourceAddressLbl.text = tSaddress
                    self.destAddLbl.text = tDAddress
                    self.sourceAddressLbl.fitText()
                    self.destAddLbl.fitText()
                    
                    let sourceAddHeight = tSaddress.height(withConstrainedWidth: Application.screenSize.width - 101, font: UIFont(name: "Roboto-Medium", size: 18)!)
                    
                    let destAddHeight = tDAddress.height(withConstrainedWidth: Application.screenSize.width - 101, font: UIFont(name: "Roboto-Medium", size: 18)!)
                    
                    let Yoffset = (sourceAddHeight - destAddHeight) / 2
                    
                    self.addressContainerCenterViewYPosition.constant = Yoffset
                    
                    self.addressContainerViewHeight.constant = 40 + sourceAddHeight + destAddHeight
                    
                    self.iTripId = dataDict.getObj(Utils.message_str).get("iTripId")
                    
                    if(tDAddress.trim() == ""){
                        self.destAddLbl.text = ""
                        self.destAddLbl.fitText()
                        self.dashedView.isHidden = true
                        self.endLocPinImgView.isHidden = true
                        self.addressContainerViewHeight.constant = sourceAddHeight + 20
                        self.addressContainerSeperatorView.isHidden = true
                        self.addressContainerCenterViewYPosition.constant = (self.addressContainerViewHeight.constant / 2)
                    }
                    
                    if(eCancelled == "Yes"){
                        self.tripGeneralInfoLbl.text = self.generalFunc.getLanguageLabel(origValue: "Trip is cancelled by driver. Reason:", key: "LBL_PREFIX_TRIP_CANCEL_DRIVER") + " \(vCancelReason)"
                        self.tripGeneralInfoLbl.isHidden = false
                        self.tripGeneralInfoLbl.fitText()
                    }else{
                        self.tripGeneralInfoLbl.isHidden = true
                        self.addressContainerViewTopMargin.constant = self.addressContainerViewTopMargin.constant - 55
                        self.PAGE_HEIGHT = self.PAGE_HEIGHT - 40
                    }
                    
                    
                    self.addFareDetails(dataDict: dataDict)
                    
//                    self.tripGeneralInfoLbl.fitText()
                    
                    
                    DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                        
                        self.dashedView.addDashedLine(color: UIColor(hex: 0xADADAD), lineWidth: 2)
                        
                        self.setPageHeight()
                    })
                    
                    self.loaderView.isHidden = true
                    self.scrollView.isHidden = false
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
    }
    
    
    func setPageHeight(){
        self.PAGE_HEIGHT = (Application.screenSize.height - 64) > (self.submitBtn.frame.maxY + 25) ? (Application.screenSize.height - 64) : (self.submitBtn.frame.maxY + 25)
        
        self.cntView.frame.size = CGSize(width: self.cntView.frame.width, height: self.PAGE_HEIGHT)
        
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)
        
    }
    
    func addFareDetails(dataDict:NSDictionary){
        
        let FareDetailsNewArr = dataDict.getObj(Utils.message_str).getArrObj("FareDetailsNewArr")
        
        for i in 0..<FareDetailsNewArr.count {
            
            let dict_temp = FareDetailsNewArr[i] as! NSDictionary
            
            for (key, value) in dict_temp {
                
                let viewCus = self.generalFunc.loadView(nibName: "FareDataItemView", uv: self, isWithOutSize: true)
                let frame = CGRect(x: 0, y: 0, width: self.fareDataContainerStkView.frame.width, height: 40)
                viewCus.frame = frame
                
                let stView = viewCus.subviews[0] as! UIStackView
                
                let lblTitle = stView.subviews[0] as! MyLabel
                let lblValue = stView.subviews[1] as! MyLabel
                
                lblTitle.text = Configurations.convertNumToAppLocal(numStr: key as! String)
                lblValue.text = Configurations.convertNumToAppLocal(numStr: value as! String)
                //                print("converted:\(lblTitle.text): \(lblValue.text)")
                self.fareDataContainerStkView.addArrangedSubview(viewCus)
                
                if(Configurations.isRTLMode()){
                    lblValue.textAlignment = .left
                }else{
                    lblValue.textAlignment = .right
                }
            }
        }
        
        self.fareContainerViewHeightTemp = CGFloat(40 * FareDetailsNewArr.count)
//        self.fareContainerViewHeight.constant = CGFloat(40 * FareDetailsNewArr.count)
        
        self.detailsViewHeightTemp = self.detailsViewHeight.constant +  self.fareContainerViewHeightTemp
        
//        self.detailsViewHeight.constant = self.detailsViewHeight.constant +  self.fareContainerViewHeight.constant
        
//        self.PAGE_HEIGHT = self.PAGE_HEIGHT + self.detailsViewHeight.constant
        
//        self.cntView.frame.size = CGSize(width: self.contentView.frame.width, height: self.PAGE_HEIGHT)
//        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)

        
    }
    
    func submitRating(fAmount:String, isCollectTip:String){
        
        if(bgTipView != nil){
            bgTipView.removeFromSuperview()
        }
        
        if(giveTipView != nil){
            giveTipView.removeFromSuperview()
        }
        
        let parameters = ["type":"submitRating","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "tripID": iTripId, "rating": "\(self.ratingBar.rating)", "message": "\(commentTxtView.text!)", "fAmount": fAmount, "isCollectTip": isCollectTip]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.loadTripFinishView()
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    self.currentTipMode = "OFF"

                }
                
            }else{
                self.generalFunc.setError(uv: self)
                self.currentTipMode = "OFF"

            }
        })
    }
    

    func loadTripFinishView(){
        let tripFinishView = self.generalFunc.loadView(nibName: "TripFinishView", uv: self, isWithOutSize: true)
        
        self.tripFinishView = tripFinishView
        
        let width = Application.screenSize.width  > 380 ? 370 : Application.screenSize.width - 50
        
        tripFinishView.frame.size = CGSize(width: width, height: 270)
        
        tripFinishView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
//        tripFinishView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        let bgView = UIView()
//        bgView.frame = self.contentView.frame
        self.tripFinishBGView = bgView
        
        bgView.frame = CGRect(x:0, y:0, width:Application.screenSize.width, height: Application.screenSize.height)
        
        bgView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        bgView.backgroundColor = UIColor.black
        bgView.alpha = 0.4
        bgView.isUserInteractionEnabled = true
        
        tripFinishView.layer.shadowOpacity = 0.5
        tripFinishView.layer.shadowOffset = CGSize(width: 0, height: 3)
        tripFinishView.layer.shadowColor = UIColor.black.cgColor
        
//        self.view.addSubview(bgView)
//        self.view.addSubview(tripFinishView)

        
        let currentWindow = Application.window
        
        if(self.navigationController != nil){
//            currentWindow?.addSubview(bgView)
//            currentWindow?.addSubview(tripFinishView)
            
            self.navigationController?.view.addSubview(bgView)
            self.navigationController?.view.addSubview(tripFinishView)
            
        }else{
            self.view.addSubview(bgView)
            self.view.addSubview(tripFinishView)
        }
        
        bgView.alpha = 0
        tripFinishView.alpha = 0
        UIView.animate(
            withDuration: 0.5,
            delay: 0,
            options: .curveEaseInOut,
            animations: {
                bgView.alpha = 0.4
                tripFinishView.alpha = 1
                
        }
        )
        Utils.createRoundedView(view: tripFinishView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        GeneralFunctions.setImgTintColor(imgView: (tripFinishView.subviews[0] as! UIImageView), color: UIColor.UCAColor.AppThemeColor)
        
        (tripFinishView.subviews[1] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "Successfully Finished", key: "LBL_SUCCESS_FINISHED")
        
        (tripFinishView.subviews[2] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TRIP_FINISHED_TXT")
        (tripFinishView.subviews[2] as! MyLabel).fitText()
        
        (tripFinishView.subviews[4] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "OK THANKS", key: "LBL_OK_THANKS").uppercased()
        
        
        let okTapGue = UITapGestureRecognizer()
        
        okTapGue.addTarget(self, action: #selector(self.tripFinishOkTapped))
        
        (tripFinishView.subviews[4] as! MyLabel).isUserInteractionEnabled = true
        
        (tripFinishView.subviews[4] as! MyLabel).addGestureRecognizer(okTapGue)
    }
    
    func loadTipView(){
        giveTipView = self.generalFunc.loadView(nibName: "GiveTipView", uv: self, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 380 ? 370 : Application.screenSize.width - 50
        
        giveTipView.frame.size = CGSize(width: width, height: 300)
        
        
        giveTipView.center = CGPoint(x: Application.screenSize.width / 2, y: Application.screenSize.height / 2)
        
        bgTipView = UIView()
        bgTipView.frame = CGRect(x: 0, y: 0, width: Application.screenSize.width, height: Application.screenSize.height)
        
        bgTipView.backgroundColor = UIColor.black
        bgTipView.alpha = 0.4
        bgTipView.isUserInteractionEnabled = true
        
        giveTipView.layer.shadowOpacity = 0.5
        giveTipView.layer.shadowOffset = CGSize(width: 0, height: 3)
        giveTipView.layer.shadowColor = UIColor.black.cgColor
        
//        self.view.addSubview(bgTipView)
//        self.view.addSubview(giveTipView)

        if(self.navigationController != nil){
            //            currentWindow?.addSubview(bgView)
            //            currentWindow?.addSubview(tripFinishView)
            
            self.navigationController?.view.addSubview(bgTipView)
            self.navigationController?.view.addSubview(giveTipView)
            
        }else{
            self.view.addSubview(bgTipView)
            self.view.addSubview(giveTipView)
        }
        
        Utils.createRoundedView(view: giveTipView, borderColor: UIColor.clear, borderWidth: 0, cornerRadius: 10)
        
        GeneralFunctions.setImgTintColor(imgView: tipHImgView, color: UIColor.UCAColor.AppThemeColor)
        
        self.giveTipPLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_GIVE_TIP_TXT")
        self.giveTipNLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_THANKS")
        self.giveTipNoteLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TIP_TXT")
        self.giveTipHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TIP_TITLE_TXT")
        self.giveTipNoteLbl.fitText()
        
        self.enterTipTxtField.isHidden = true
        self.enterTipTxtField.getTextField()!.keyboardType = .decimalPad
        
        self.giveTipNLbl.setClickDelegate(clickDelegate: self)
        self.giveTipPLbl.setClickDelegate(clickDelegate: self)
        
        
        self.enterTipTxtField.getTextField()!.keyboardType = .numberPad
        
    }
    
    func myLableTapped(sender: MyLabel) {
        if(sender == self.giveTipPLbl){
            
            if(currentTipMode == "ON"){
                let tipEntered = Utils.checkText(textField: self.enterTipTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.enterTipTxtField.getTextField()!, error: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT"))
                
                if(tipEntered){
                    submitRating(fAmount: "\(Utils.getText(textField: self.enterTipTxtField.getTextField()!))", isCollectTip: "Yes")
                }
            }else{
                
                self.enterTipTxtField.isHidden = false
                self.giveTipNoteLbl.isHidden = true
                
                self.giveTipPLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT")
                self.giveTipNLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SKIP_TXT")
                self.giveTipHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TIP_AMOUNT_ENTER_TITLE")
                
                currentTipMode = "ON"
            }
            
        }else if(sender == self.giveTipNLbl){
            submitRating(fAmount: "", isCollectTip: "No")
        }
    }
    
    func giveTip(){
        
        let parameters = ["type":"collectTip","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iTripId": iTripId, "fAmount": "\(Utils.getText(textField: self.enterTipTxtField.getTextField()!))"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.tripFinishOkTapped()
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")) + " " + dataDict.get("minValue"))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func tripFinishOkTapped(){
        if(tripFinishBGView != nil){
            tripFinishBGView.removeFromSuperview()
        }
        
        if(tripFinishView != nil){
            tripFinishView.removeFromSuperview()
        }
        
        if(self.navigationItem != nil && self.navigationItem.leftBarButtonItem != nil){
            self.closeCurrentScreen()
        }else{
            let getUserData = GetUserData(uv: self, window: self.window!)
            getUserData.getdata()
        }
    }
}
