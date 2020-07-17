//
//  AddVehiclesUV.swift
//  DriverApp
//
//  Created by NEW MAC on 02/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AddVehiclesUV: UIViewController, MyBtnClickDelegate, BEMCheckBoxDelegate, MyTxtFieldClickDelegate {
    
    @IBOutlet weak var contentView: UIView!
    
    @IBOutlet weak var vehicleTypeTxtField: MyTextField!
    @IBOutlet weak var vehicleTypeHeight: NSLayoutConstraint!
    @IBOutlet weak var makeTxtField: MyTextField!
    @IBOutlet weak var modelTxtField: MyTextField!
    @IBOutlet weak var yearTxtField: MyTextField!
    @IBOutlet weak var colorTxtField: MyTextField!
    @IBOutlet weak var licPlatTxtField: MyTextField!
    @IBOutlet weak var serviceContainerView: UIStackView!
    @IBOutlet weak var serviceContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var submitBtn: MyButton!
    @IBOutlet weak var handiCapView: UIView!
    @IBOutlet weak var handiCapLbl: MyLabel!
    @IBOutlet weak var handiCapChkBox: BEMCheckBox!
    @IBOutlet weak var handiCapViewHeight: NSLayoutConstraint!
    @IBOutlet weak var vehicleInfoAreaHeight: NSLayoutConstraint!
    @IBOutlet weak var vehicleInfoAreaView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    
    var userProfileJson:NSDictionary!
    
    let generalFunc = GeneralFunctions()
    
    var iDriverVehicleId = ""
    var currentVehicleData:NSDictionary!
    
    var isFromMainPage = false
    var mainScreenUv:MainScreenUV!
    
    var isPageLoaded = false
    
    var cntView:UIView!
    
    var loaderView:UIView!
    
    var window:UIWindow!
    
    var carlistArr = [NSDictionary]()
    var yearListArr = [String]()
    var carTypeArr = [NSDictionary]()
    
    var makeListArr = [String]()
    var modelListArr = [String]()
    var modelsArr = [NSDictionary]()
    
    var selectedMakeId = -1
    var selectedModelId = -1
    var selectedYearId = -1
    
    var carTypesStatusArr = [Bool]()
    
    var PAGE_HEIGHT:CGFloat = 630
    
    var isSafeAreaSet = false
    
    var eType = Utils.cabGeneralType_Ride
    
    var manageVehiUV:ManageVehiclesUV!
    
    var isFromDriverStatesUV = false
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        window = Application.window!
        
        
        cntView = self.generalFunc.loadView(nibName: "AddVehiclesScreenDesign", uv: self, contentView: scrollView)
        
        scrollView.addSubview(cntView)
        
        scrollView.isHidden = true
        scrollView.bounces = false
        scrollView.backgroundColor = UIColor.clear
        
        if(Configurations.isIponeXDevice()){
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 20
        }
        //        self.contentView.addSubview(self.generalFunc.loadView(nibName: "AddVehiclesScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        //        setData()
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        let HANDICAP_ACCESSIBILITY_OPTION = userProfileJson.get("HANDICAP_ACCESSIBILITY_OPTION")
        //        HANDICAP_ACCESSIBILITY_OPTION = "Yes"
        if(HANDICAP_ACCESSIBILITY_OPTION.uppercased() != "YES"){
            hideHandiCappedView(isHide: true)
        }else{
            handiCapView.isHidden = false
            self.handiCapChkBox.boxType = .square
            self.handiCapChkBox.offAnimationType = .bounce
            self.handiCapChkBox.onAnimationType = .bounce
            self.handiCapChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
            self.handiCapChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
            self.handiCapChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
            self.handiCapChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
            self.handiCapLbl.text = self.generalFunc.getLanguageLabel(origValue: "Handicap accessibility available?", key: "LBL_HANDICAP_QUESTION")
        }
        
        if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.vehicleInfoAreaHeight.constant = 0
            self.vehicleInfoAreaView.isHidden = true
        }
        
        self.vehicleTypeHeight.constant = 0
        self.vehicleTypeTxtField.isHidden = true
        self.vehicleInfoAreaHeight.constant = 450 + handiCapViewHeight.constant - 60
        
        if(userProfileJson.get("APP_TYPE").uppercased() == Utils.cabGeneralType_Ride_Deliver.uppercased()){
            self.vehicleTypeHeight.constant = 60
            self.vehicleTypeTxtField.isHidden = false
            
            self.vehicleInfoAreaHeight.constant = self.vehicleInfoAreaHeight.constant + 60
            
            self.eType = Utils.cabGeneralType_Ride_Deliver
            self.vehicleTypeTxtField.setText(text:"\(self.generalFunc.getLanguageLabel(origValue: "Ride", key: "LBL_RIDE"))-\(self.generalFunc.getLanguageLabel(origValue: "Delivery", key: "LBL_DELIVERY"))")
            
        }else if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_Ride){
            self.eType = Utils.cabGeneralType_Ride
            self.vehicleTypeTxtField.setText(text: "\(self.generalFunc.getLanguageLabel(origValue: "Ride", key: "LBL_RIDE"))")
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 75
        }
        else if(userProfileJson.get("APP_TYPE") == "Delivery"){
            self.eType = Utils.cabGeneralType_Deliver
            self.vehicleTypeTxtField.setText(text: "\(self.generalFunc.getLanguageLabel(origValue: "Delivery", key: "LBL_DELIVERY"))")
            self.PAGE_HEIGHT = self.PAGE_HEIGHT - 75
        }
        
        vehicleTypeTxtField.disableMenu()
        makeTxtField.disableMenu()
        yearTxtField.disableMenu()
        modelTxtField.disableMenu()
        
        setData()
    }
    
    func hideHandiCappedView(isHide:Bool){
        if(isHide == true || userProfileJson.get("HANDICAP_ACCESSIBILITY_OPTION").uppercased() != "YES"){
            if(handiCapView.isHidden == false){
                handiCapViewHeight.constant = 0
                handiCapView.isHidden = true
                self.handiCapChkBox.on = false
                PAGE_HEIGHT = PAGE_HEIGHT - 45
                self.vehicleInfoAreaHeight.constant = self.vehicleInfoAreaHeight.constant - 45
            }
        }else{
            if(handiCapView.isHidden == true){
                handiCapViewHeight.constant = 45
                handiCapView.isHidden = false
                self.handiCapChkBox.on = false
                PAGE_HEIGHT = PAGE_HEIGHT + 45
                self.vehicleInfoAreaHeight.constant = self.vehicleInfoAreaHeight.constant + 45
            }
        }
        
        cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT + GeneralFunctions.getSafeAreaInsets().bottom)
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT + GeneralFunctions.getSafeAreaInsets().bottom)
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: PAGE_HEIGHT + GeneralFunctions.getSafeAreaInsets().bottom)
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: PAGE_HEIGHT + GeneralFunctions.getSafeAreaInsets().bottom)
            
            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(0.5 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
                self.makeTxtField.addArrowView(color: UIColor(hex: 0xbcbcbc), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
                self.modelTxtField.addArrowView(color: UIColor(hex: 0xbcbcbc), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
                self.yearTxtField.addArrowView(color: UIColor(hex: 0xbcbcbc), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
                self.vehicleTypeTxtField.addArrowView(color: UIColor(hex: 0xbcbcbc), transform: CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180)))
            })
            
            getData(isCarTypeOnly: false)
            
            isPageLoaded = true
        }
    }
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            
            if(cntView != nil){
                cntView.frame.size.height = scrollView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            }
            
            isSafeAreaSet = true
        }
    }
    
    func setData(){
        if(userProfileJson.get("APP_TYPE") == Utils.cabGeneralType_UberX){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES")
        }else{
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_VEHICLE")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_VEHICLE")
        }
        
        self.vehicleTypeTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Vehicle Type", key: "LBL_VEHICLE_TYPE_SMALL_TXT"))
        self.makeTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MAKE"))
        self.modelTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MODEL"))
        self.yearTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YEAR"))
        self.colorTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COLOR_TXT"))
        self.licPlatTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LICENCE_PLATE_TXT"))
        
        self.submitBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_SUBMIT_TXT"))
        
        self.vehicleTypeTxtField.setEnable(isEnabled: false)
        self.vehicleTypeTxtField.myTxtFieldDelegate = self
        
        self.makeTxtField.setEnable(isEnabled: false)
        self.makeTxtField.myTxtFieldDelegate = self
        
        self.modelTxtField.setEnable(isEnabled: false)
        self.modelTxtField.myTxtFieldDelegate = self
        
        self.yearTxtField.setEnable(isEnabled: false)
        self.yearTxtField.myTxtFieldDelegate = self
        
        self.vehicleTypeTxtField.getTextField()!.clearButtonMode = .never
        self.makeTxtField.getTextField()!.clearButtonMode = .never
        self.modelTxtField.getTextField()!.clearButtonMode = .never
        self.yearTxtField.getTextField()!.clearButtonMode = .never
        
        self.submitBtn.clickDelegate = self
        
        if(self.currentVehicleData != nil){
            self.licPlatTxtField.setText(text: self.currentVehicleData!.get("vLicencePlate"))
            self.colorTxtField.setText(text: self.currentVehicleData!.get("vColour"))
            
            if(self.currentVehicleData!.get("eHandiCapAccessibility") == "Yes"){
                self.handiCapChkBox.on = true
            }
            
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Edit Vehicle", key: "LBL_EDIT_VEHICLE")
            self.title = self.generalFunc.getLanguageLabel(origValue: "Edit Vehicle", key: "LBL_EDIT_VEHICLE")
        }
        
        
        self.licPlatTxtField.regexToMatch = "^[a-zA-Z0-9 ]+$"
        
        //        if iDriverVehicleId != ""
        //        {
        //            self.licPlatTxtField.setEnable(isEnabled: false)
        //            self.colorTxtField.setEnable(isEnabled: false)
        //            self.licPlatTxtField.myTxtFieldDelegate = self
        //            self.colorTxtField.myTxtFieldDelegate = self
        //        }
    }
    
    func myTxtFieldTapped(sender: MyTextField){
        
        //        if(self.colorTxtField != nil && sender == self.colorTxtField){
        //            if iDriverVehicleId != ""
        //            {
        //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHI_RESTRICTION_TXT"))
        //                return
        //            }
        //        }
        //        if(self.licPlatTxtField != nil && sender == self.licPlatTxtField){
        //            if iDriverVehicleId != ""
        //            {
        //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHI_RESTRICTION_TXT"))
        //                return
        //            }
        //        }
        
        if(self.makeTxtField != nil && sender == self.makeTxtField){
            
            //            if iDriverVehicleId != ""
            //            {
            //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHI_RESTRICTION_TXT"))
            //                return
            //            }
            let openListView = OpenListView(uv: self, containerView: self.view)
            openListView.show(listObjects: self.makeListArr, title: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_MAKE"), currentInst: openListView, handler: { (selectedItemId) in
                self.makeChanged(selectedIndex: selectedItemId)
            })
            
        }else if(modelTxtField != nil && sender == modelTxtField){
            
            //            if iDriverVehicleId != ""
            //            {
            //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHI_RESTRICTION_TXT"))
            //                return
            //            }
            
            if(self.selectedMakeId != -1){
                let openListView = OpenListView(uv: self, containerView: self.view)
                openListView.show(listObjects: self.modelListArr, title: self.generalFunc.getLanguageLabel(origValue: "Select model", key: "LBL_SELECT_MODEL"), currentInst: openListView, handler: { (selectedItemId) in
                    self.modelChanged(selectedIndex: selectedItemId)
                })
            }else{
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "Please select make", key: "LBL_SELECT_MAKE_HINT"), uv: self)
            }
        }else if(yearTxtField != nil && sender == yearTxtField){
            
            //            if iDriverVehicleId != ""
            //            {
            //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHI_RESTRICTION_TXT"))
            //                return
            //            }
            
            let openListView = OpenListView(uv: self, containerView: self.view)
            openListView.show(listObjects: self.yearListArr, title: self.generalFunc.getLanguageLabel(origValue: "Select Year", key: "LBL_SELECT_YEAR"), currentInst: openListView, handler: { (selectedItemId) in
                self.yearChanged(selectedIndex: selectedItemId)
            })
        }else if(vehicleTypeTxtField != nil && sender == vehicleTypeTxtField){
            
            //            if iDriverVehicleId != ""
            //            {
            //                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHI_RESTRICTION_TXT"))
            //                return
            //            }
            
            let dataArr = ["\(self.generalFunc.getLanguageLabel(origValue: "Ride", key: "LBL_RIDE"))","\(self.generalFunc.getLanguageLabel(origValue: "Delivery", key: "LBL_DELIVERY"))","\(self.generalFunc.getLanguageLabel(origValue: "Ride", key: "LBL_RIDE"))-\(self.generalFunc.getLanguageLabel(origValue: "Delivery", key: "LBL_DELIVERY"))"]
            let openListView = OpenListView(uv: self, containerView: self.view)
            openListView.show(listObjects: dataArr, title: self.generalFunc.getLanguageLabel(origValue: "Select Vehicle Type", key: "LBL_SELECT_VEHICLE_TYPE"), currentInst: openListView, handler: { (selectedItemId) in
                if(selectedItemId == 0){
                    self.eType = Utils.cabGeneralType_Ride
                    self.vehicleTypeTxtField.setText(text:"\(self.generalFunc.getLanguageLabel(origValue: "Ride", key: "LBL_RIDE"))")
                    self.hideHandiCappedView(isHide: false)
                }else if(selectedItemId == 1){
                    self.eType = Utils.cabGeneralType_Deliver
                    self.vehicleTypeTxtField.setText(text:"\(self.generalFunc.getLanguageLabel(origValue: "Delivery", key: "LBL_DELIVERY"))")
                    self.hideHandiCappedView(isHide: true)
                }else{
                    self.hideHandiCappedView(isHide: false)
                    self.eType = Utils.cabGeneralType_Ride_Deliver
                    self.vehicleTypeTxtField.setText(text:"\(self.generalFunc.getLanguageLabel(origValue: "Ride", key: "LBL_RIDE"))-\(self.generalFunc.getLanguageLabel(origValue: "Delivery", key: "LBL_DELIVERY"))")
                }
                
                self.getData(isCarTypeOnly: true)
            })
        }
    }
    
    
    func getData(isCarTypeOnly:Bool){
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.view)
            loaderView.isHidden = false
        }else{
            loaderView.isHidden = false
        }
        loaderView.backgroundColor = UIColor.clear
        
        scrollView.isHidden = true
        
        let parameters = ["type":"getUserVehicleDetails","iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "eType": self.eType]
        Utils.printLog(msgData: "Parameter:\(parameters)")
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                if(dataDict.get("Action") == "1"){
                    
                    self.loaderView.isHidden = true
                    self.scrollView.isHidden = false
                    
                    let yearArr = dataDict.getObj(Utils.message_str).getArrObj("year")
                    
                    let carlist = dataDict.getObj(Utils.message_str).getArrObj("carlist")
                    let vehicletypelist = dataDict.getObj(Utils.message_str).getArrObj("vehicletypelist")
                    
                    self.yearListArr.removeAll()
                    self.carTypeArr.removeAll()
                    self.carTypesStatusArr.removeAll()
                    self.carlistArr.removeAll()
                    self.makeListArr.removeAll()
                    
                    for i in 0..<yearArr.count{
                        self.yearListArr += [Configurations.convertNumToAppLocal(numStr: yearArr[i] as! String)]
                        
                        if(self.currentVehicleData != nil && (yearArr[i] as! String) == self.currentVehicleData.get("iYear") && isCarTypeOnly == false){
                            self.selectedYearId = i
                        }
                    }
                    
                    var vCarType = [String]()
                    
                    if(self.currentVehicleData != nil && self.currentVehicleData!.get("vCarType") != ""){
                        vCarType = self.currentVehicleData!.get("vCarType").components(separatedBy: ",")
                    }
                    
                    if(vehicletypelist.count == 0){
                        
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message1")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT").uppercased(), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT").uppercased(), completionHandler: { (btnClickedId) in
                            
                            if(btnClickedId == 1){
                                if(self.isFromMainPage == true && self.mainScreenUv != nil){
                                    self.closeCurrentScreen()
                                    if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                                        self.mainScreenUv.openManageVehiclesScreen()
                                    }
                                    return
                                }
                                
                                if self.isFromDriverStatesUV{
                                    self.closeCurrentScreen()
                                    return
                                }
                                
                                if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                                    self.performSegue(withIdentifier: "unwindToManageVehicles", sender: self)
                                }
                            }else{
                                let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                                contactUsUv.manageVehiUV = self.manageVehiUV
                                (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(contactUsUv, animated: true)
                            }
                        })
                        return
                    }
                    
                    for i in 0..<vehicletypelist.count{
                        let item = vehicletypelist[i] as! NSDictionary
                        self.carTypeArr += [item]
                        self.carTypesStatusArr += [false]
                        
                        if((self.currentVehicleData != nil && vCarType.contains(item.get("iVehicleTypeId"))) || (item.get("VehicleServiceStatus") != "" && item.get("VehicleServiceStatus") == "true")){
                            self.carTypesStatusArr[i] = true
                        }
                        
                    }
                    
                    for i in 0..<carlist.count{
                        let item = carlist[i] as! NSDictionary
                        self.makeListArr += [item.get("vMake")]
                        
                        if(self.currentVehicleData != nil && item.get("iMakeId") == self.currentVehicleData.get("iMakeId") && isCarTypeOnly == false){
                            self.selectedMakeId = i
                        }
                        
                        self.carlistArr += [item]
                    }
                    
                    self.generateCarTypes(isCarTypeOnly: isCarTypeOnly)
                    
                    
                    if(self.selectedMakeId != -1 && isCarTypeOnly == false){
                        self.makeChanged(selectedIndex: self.selectedMakeId)
                    }
                    
                    if(self.selectedYearId != -1 && isCarTypeOnly == false){
                        self.yearChanged(selectedIndex: self.selectedYearId)
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            
        })
    }
    
    
    func makeChanged(selectedIndex:Int){
        
        self.selectedMakeId = selectedIndex
        
        //        if(selectedIndex != -1){
        let item = self.carlistArr[selectedIndex]
        let vModellist = item.getArrObj("vModellist")
        
        self.makeTxtField.setText(text: self.makeListArr[selectedIndex])
        self.modelListArr.removeAll()
        self.modelsArr.removeAll()
        
        var selectedModelPosition = -1
        for i in 0..<vModellist.count{
            let item = vModellist[i] as! NSDictionary
            self.modelListArr += [item.get("vTitle")]
            
            self.modelsArr += [item]
            
            if(self.currentVehicleData != nil && item.get("iModelId") == self.currentVehicleData.get("iModelId")){
                selectedModelPosition = i
            }
        }
        
        self.selectedModelId = selectedModelPosition
        
        if(self.selectedModelId != -1){
            self.modelTxtField.setText(text: "\(self.modelListArr[self.selectedModelId])")
        }else{
            self.modelTxtField.setText(text: "")
        }
        
        
        //        }else{
        //            self.modelTxtField.setEnable(isEnabled: false)
        //            self.modelTxtField.myTxtFieldDelegate = self
        //        }
        
    }
    
    func yearChanged(selectedIndex:Int){
        self.selectedYearId = selectedIndex
        self.yearTxtField.setText(text: self.yearListArr[selectedIndex])
    }
    
    func modelChanged(selectedIndex:Int){
        self.selectedModelId = selectedIndex
        self.modelTxtField.setText(text: self.modelListArr[selectedIndex])
    }
    
    func generateCarTypes(isCarTypeOnly:Bool){
        
        if(isCarTypeOnly == true){
            for subview in self.serviceContainerView.subviews {
                self.PAGE_HEIGHT = self.PAGE_HEIGHT - 60
                subview.removeFromSuperview()
            }
            self.serviceContainerView.layoutSubviews()
            self.serviceContainerView.layoutIfNeeded()
        }
        for i in 0..<carTypeArr.count{
            let item = carTypeArr[i]
            
            let carTypeItemView = CarTypeItemView(frame: CGRect(x:0,y:0, width: self.serviceContainerView.frame.width, height: 60))
            //            let viewCus = self.generalFunc.loadView(nibName: "CarTypeItemView", uv: self, isWithOutSize: true)
            carTypeItemView.carTypeNameLbl.text = item.get("vVehicleType")
            carTypeItemView.subTitleLbl.text = item.get("SubTitle")
            
            carTypeItemView.vtypeLbl.isHidden = true
            
            if(self.eType.uppercased() == Utils.cabGeneralType_Ride_Deliver.uppercased()){
                carTypeItemView.vtypeLbl.isHidden = false
                carTypeItemView.vtypeLbl.text = String(format:"(%@)",item.get("eType"))
            }
            
            carTypeItemView.carTypeChkBox.tag = i
            carTypeItemView.carTypeChkBox.boxType = .square
            carTypeItemView.carTypeChkBox.offAnimationType = .bounce
            carTypeItemView.carTypeChkBox.onAnimationType = .bounce
            carTypeItemView.carTypeChkBox.onCheckColor = UIColor.UCAColor.AppThemeTxtColor
            carTypeItemView.carTypeChkBox.onFillColor = UIColor.UCAColor.AppThemeColor
            carTypeItemView.carTypeChkBox.onTintColor = UIColor.UCAColor.AppThemeColor
            carTypeItemView.carTypeChkBox.tintColor = UIColor.UCAColor.AppThemeColor_1
            //            carTypeSwitch.addTarget(self, action: #selector(self.carTypeStatusChanged), for: .valueChanged)
            
            if(self.carTypesStatusArr[i] == true){
                carTypeItemView.carTypeChkBox.setOn(true, animated: true)
            }
            carTypeItemView.carTypeChkBox.delegate = self
            
            self.serviceContainerView.addArrangedSubview(carTypeItemView)
            self.PAGE_HEIGHT = self.PAGE_HEIGHT + 60
            
        }
        
        self.serviceContainerView.frame.size = CGSize(width: self.view.frame.width, height: CGFloat(self.carTypeArr.count * 60))
        self.serviceContainerViewHeight.constant = CGFloat(self.carTypeArr.count * 60)
        
        _ = self.contentView.frame.height + self.serviceContainerViewHeight.constant + 40
        
        
        self.cntView.frame.size = CGSize(width: self.contentView.frame.width, height: self.PAGE_HEIGHT)
        self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.PAGE_HEIGHT)
    }
    
    func didTap(_ checkBox: BEMCheckBox) {
        self.carTypesStatusArr[checkBox.tag] = checkBox.on
    }
    
    
    //    func carTypeStatusChanged(sender:UISwitch){
    //        self.carTypesStatusArr[sender.tag] = sender.isOn
    //    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.submitBtn){
            checkData()
        }
    }
    
    func checkData(){
        
        self.view.endEditing(true)
        
        if(userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
            if(self.selectedMakeId == -1){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHOOSE_MAKE"), uv: self)
                return
            }
            
            
            if(self.selectedModelId == -1){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHOOSE_VEHICLE_MODEL"), uv: self)
                return
            }
            
            
            if(self.selectedYearId == -1){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHOOSE_YEAR"), uv: self)
                return
            }
            
            if(Utils.checkText(textField: self.licPlatTxtField.getTextField()!) == false){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "Please add your car's licence plate no.", key: "LBL_ADD_LICENCE_PLATE"), uv: self)
                return
            }
        }
        
        var isCarTypeSelected = false
        var carTypes_str = ""
        for i in 0..<self.carTypesStatusArr.count{
            if(self.carTypesStatusArr[i] == true){
                isCarTypeSelected = true
                let carTypeId = self.carTypeArr[i].get("iVehicleTypeId")
                carTypes_str = carTypes_str == "" ? carTypeId : (carTypes_str + "," + carTypeId)
            }
        }
        
        if(isCarTypeSelected == false){
            Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_CAR_TYPE"), uv: self)
            return
        }
        
        if(userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
            let vMakeId = self.carlistArr[self.selectedMakeId].get("iMakeId")
            let vModelId = self.modelsArr[self.selectedModelId].get("iModelId")
            
            if userProfileJson.get("ENABLE_EDIT_DRIVER_VEHICLE").uppercased() == "NO"{
                if iDriverVehicleId == ""{
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_COMFIRM_ADD_VEHICLE"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONFIRM_TXT").uppercased(), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT").uppercased(), completionHandler: { (btnClickedId) in
                        
                        if btnClickedId == 0{
                            self.updateCarDetails(carTypes: carTypes_str, vMakeId: vMakeId, vModelId: vModelId)
                        }
                    })
                }else{
                    self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_EDIT_VEHICLE_DISABLED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT").uppercased() , nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT").uppercased(), completionHandler: { (btnClickedId) in
                        
                        if(btnClickedId == 1){
                            let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                            self.pushToNavController(uv: contactUsUv)
                        }
                    })
                }
                return
            }
            updateCarDetails(carTypes: carTypes_str, vMakeId: vMakeId, vModelId: vModelId)
        }else{
            
            updateCarDetails(carTypes: carTypes_str, vMakeId: "", vModelId: "")
        }
    }
    
    func updateCarDetails(carTypes:String, vMakeId:String, vModelId:String){
        
        let parameters = ["type":"UpdateDriverVehicle","iDriverId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "iMakeId": vMakeId, "iModelId": vModelId, "iYear": Configurations.convertNumToEnglish(numStr:  Utils.getText(textField: self.yearTxtField.getTextField()!)), "vLicencePlate": Utils.getText(textField: self.licPlatTxtField.getTextField()!), "vCarType": carTypes, "iDriverVehicleId": iDriverVehicleId, "vColor": Utils.getText(textField: self.colorTxtField.getTextField()!), "HandiCap": "\(self.handiCapChkBox.on == true ? "Yes" : "No")", "eType": "\(self.eType)"]
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    if(self.iDriverVehicleId == ""){
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_UPLOAD_DOC").uppercased(), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SKIP_TXT").uppercased(), completionHandler: { (btnClickedId) in
                            
                            if(btnClickedId == 1){
                                if(self.isFromMainPage == true && self.mainScreenUv != nil){
                                    self.closeCurrentScreen()
//                                    if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
//                                        self.mainScreenUv.openManageVehiclesScreen()
//                                    }
                                    return
                                }
                                
                                if self.isFromDriverStatesUV{
                                    self.closeCurrentScreen()
                                    return
                                }
                                
                                if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                                    self.performSegue(withIdentifier: "unwindToManageVehicles", sender: self)
                                }
                            }else{
                                let listOfDocumentUV = GeneralFunctions.instantiateViewController(pageName: "ListOfDocumentUV") as! ListOfDocumentUV
                                listOfDocumentUV.LIST_TYPE = "vehicle"
                                listOfDocumentUV.iDriverVehicleId = dataDict.get("VehicleInsertId")
                                listOfDocumentUV.fromAddVehicle = true
                                listOfDocumentUV.manageVehiUV = self.manageVehiUV
                                self.pushToNavController(uv: listOfDocumentUV)
                            }
                            
                        })
                    }else{
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                            
                            if(self.isFromMainPage == true && self.mainScreenUv != nil){
                                self.closeCurrentScreen()
                                if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                                    self.mainScreenUv.openManageVehiclesScreen()
                                }
                                return
                            }
                            
                            if(self.userProfileJson.get("APP_TYPE") != Utils.cabGeneralType_UberX){
                                self.performSegue(withIdentifier: "unwindToManageVehicles", sender: self)
                            }
                        })
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
        
    }
    
}
