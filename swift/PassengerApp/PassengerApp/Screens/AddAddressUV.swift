//
//  AddAddressUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 09/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AddAddressUV: UIViewController, MyBtnClickDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var serviceAreaHLbl: MyLabel!
    @IBOutlet weak var serviceAreaVLbl: MyLabel!
    @IBOutlet weak var myLocImgView: UIImageView!
    @IBOutlet weak var serviceAreaVContainerViewHeight: NSLayoutConstraint!
    @IBOutlet weak var serviceAreaVContainerView: UIView!
    @IBOutlet weak var serviceAddHLbl: MyLabel!
    @IBOutlet weak var buildingTxtField: MyTextField!
    @IBOutlet weak var landMarkTxtField: MyTextField!
    @IBOutlet weak var addressTypeTxtField: MyTextField!
    @IBOutlet weak var addAddressBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    
    var userProfileJson:NSDictionary!
    
    var isDirectOpen = false
    
    var bookingType = ""
    
    var ufxSelectedVehicleTypeId = ""
    var ufxSelectedVehicleTypeName = ""
    var ufxSelectedLatitude = ""
    var ufxSelectedLongitude = ""
    var ufxSelectedAddress = ""
    var ufxSelectedQty = ""
    
    var ufxServiceItemDict:NSDictionary!
    
    let myLocTapGue = UITapGestureRecognizer()
    let serviceAreaTapGue = UITapGestureRecognizer()
    
    var currentSelectedLocation:CLLocation!
    
    var isScreenKilled = false

    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
        
        
        GeneralFunctions.postNotificationSignal(key: ConfigPubNub.resumeInst_key, obj: self)
        
        if(isScreenKilled == true){
            Utils.closeCurrentScreen(isAnimated: false, uv: self)
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "AddAddressScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
//
//        self.myLocTapGue.addTarget(self, action: #selector(self.myLocImgTapped))
//        self.myLocImgView.isUserInteractionEnabled = true
//        self.myLocImgView.addGestureRecognizer(myLocTapGue)
        
        self.serviceAreaVContainerView.isUserInteractionEnabled = true
        self.serviceAreaTapGue.addTarget(self, action: #selector(self.serviceAreaTapped))
        self.serviceAreaVContainerView.addGestureRecognizer(self.serviceAreaTapGue)
        
        setData()
    }

    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Add New Address", key: "LBL_ADD_NEW_ADDRESS_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Add New Address", key: "LBL_ADD_NEW_ADDRESS_TXT")
        self.serviceAreaHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Area of service", key: "LBL_AREA_SERVICE_HINT_INFO")
        
        self.serviceAreaVLbl.text = self.generalFunc.getLanguageLabel(origValue: "Select your area", key: "LBL_SELECT_YOUR_AREA")
        
        self.serviceAddHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Service address", key: "LBL_SERVICE_ADDRESS_HINT_INFO")
        self.buildingTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Building/House/Flat No.", key: "LBL_JOB_LOCATION_HINT_INFO"))
        self.landMarkTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Landmark(e.g hospital,park etc.)", key: "LBL_LANDMARK_HINT_INFO"))
        self.addressTypeTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "Nickname(optional-home,office etc.)", key: "LBL_ADDRESSTYPE_HINT_INFO"))
        
        self.addAddressBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Save", key: "LBL_SAVE_ADDRESS_TXT"))
        self.addAddressBtn.clickDelegate = self
        
        if(self.ufxSelectedLatitude != "" && self.ufxSelectedLongitude != ""){
            self.currentSelectedLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLatitude), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: self.ufxSelectedLongitude))
            
            var addressHeight = self.ufxSelectedAddress.height(withConstrainedWidth: Application.screenSize.width - 50, font: UIFont(name: "Roboto-Light", size: 16)!)
            if(addressHeight < 50){
                addressHeight = 50
            }
            if(addressHeight > 150){
                addressHeight = 150
            }

            serviceAreaVContainerViewHeight.constant = addressHeight
            self.serviceAreaVLbl.text = self.ufxSelectedAddress
        }
    }
   

    func serviceAreaTapped(){
        let launchPlaceFinder = LaunchPlaceFinder(viewControllerUV: self)
        launchPlaceFinder.currInst = launchPlaceFinder
        launchPlaceFinder.fromAddAddress = true
        
        
        launchPlaceFinder.initializeFinder { (address, latitude, longitude) in
            
            //            self.selectedLatitude = "\(latitude)"
            //            self.selectedLongitude = "\(longitude)"
            //            self.enterLocLbl.text = address + " ⌄"
            
                self.currentSelectedLocation = CLLocation(latitude: latitude, longitude: longitude)
//
//                self.selectedLatitude = "\(latitude)"
//                self.selectedLongitude = "\(longitude)"
//                self.enterLocLbl.text = address + " ⌄"
            
            var addressHeight = address.height(withConstrainedWidth: Application.screenSize.width - 50, font: UIFont(name: "Roboto-Light", size: 16)!)
            if(addressHeight < 50){
                addressHeight = 50
            }
            if(addressHeight > 150){
                addressHeight = 150
            }
            self.serviceAreaVContainerViewHeight.constant = addressHeight
                
            self.serviceAreaVLbl.text = address
                
           
        }
    }
    
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.addAddressBtn){
            checkData()
        }
    }
    
    func checkData(){
        let required_str = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FEILD_REQUIRD_ERROR_TXT")
        
        if(self.currentSelectedLocation == nil){
            Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SET_LOCATION"), uv: self)
            return
        }
        
        let buildingEntered = Utils.checkText(textField: self.buildingTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.buildingTxtField.getTextField()!, error: required_str)
        let landMarkEntered = Utils.checkText(textField: self.landMarkTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.landMarkTxtField.getTextField()!, error: required_str)
//        let addTypeEntered = Utils.checkText(textField: self.addressTypeTxtField.getTextField()!) ? true : Utils.setErrorFields(textField: self.addressTypeTxtField.getTextField()!, error: required_str)
        
        if (buildingEntered == false || landMarkEntered == false) {
//            || addTypeEntered == false
            return;
        }
        
        self.addAddress()
    }
    
    
    func addAddress(){
        
        let parameters = ["type":"UpdateUserAddressDetails","iUserId": GeneralFunctions.getMemberd(), "vBuildingNo": Utils.getText(textField: self.buildingTxtField.getTextField()!), "UserType": Utils.appUserType, "vLandmark": Utils.getText(textField: self.landMarkTxtField.getTextField()!), "vServiceAddress": self.serviceAreaVLbl.text!, "vAddressType": Utils.getText(textField: self.addressTypeTxtField.getTextField()!), "vLatitude": "\(self.currentSelectedLocation.coordinate.latitude)", "vLongitude": "\(self.currentSelectedLocation.coordinate.longitude)", "iSelectVehicalId": ufxSelectedVehicleTypeId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                  
                    self.isScreenKilled = true
                    
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    let address = (parameters as NSDictionary).get("vAddressType") + "\n" + (parameters as NSDictionary).get("vBuildingNo") + ", " + (parameters as NSDictionary).get("vLandmark") + "\n" + (parameters as NSDictionary).get("vServiceAddress")

                    if(dataDict.get("IsProceed").uppercased() == "NO"){
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Service is not available in your area.", key: "LBL_JOB_LOCATION_NOT_ALLOWED"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                            
                            self.closeCurrentScreen()
                        })
                        return
                    }
                    
                    if(self.isDirectOpen == true){
                        if(self.bookingType == "LATER"){
                            let chooseServiceDateUv = GeneralFunctions.instantiateViewController(pageName: "ChooseServiceDateUV") as! ChooseServiceDateUV
                            chooseServiceDateUv.ufxSelectedVehicleTypeId = self.ufxSelectedVehicleTypeId
                            chooseServiceDateUv.ufxSelectedVehicleTypeName = self.ufxSelectedVehicleTypeName
                            chooseServiceDateUv.ufxSelectedQty = self.ufxSelectedQty
                            chooseServiceDateUv.ufxAddressId = dataDict.get("AddressId")
                            chooseServiceDateUv.ufxSelectedLatitude = "\(self.currentSelectedLocation.coordinate.latitude)"
                            chooseServiceDateUv.ufxSelectedLongitude = "\(self.currentSelectedLocation.coordinate.longitude)"
                            chooseServiceDateUv.isDirectOpenFromUFXAddress = self.isDirectOpen
                            chooseServiceDateUv.serviceAreaAddress = address
                            chooseServiceDateUv.ufxServiceItemDict = self.ufxServiceItemDict
                            self.pushToNavController(uv: chooseServiceDateUv)
                        }else{
                            let mainScreenUv = GeneralFunctions.instantiateViewController(pageName: "MainScreenUV") as! MainScreenUV
                            mainScreenUv.ufxSelectedVehicleTypeId = self.ufxSelectedVehicleTypeId
                            mainScreenUv.ufxSelectedVehicleTypeName = self.ufxSelectedVehicleTypeName
                            mainScreenUv.ufxSelectedLatitude = self.ufxSelectedLatitude
                            mainScreenUv.ufxSelectedLongitude = self.ufxSelectedLongitude
                            mainScreenUv.ufxSelectedQty = self.ufxSelectedQty
                            mainScreenUv.isDirectOpenFromUFXAddress = self.isDirectOpen
                            mainScreenUv.ufxAddressId = dataDict.get("AddressId")
                            mainScreenUv.ufxSelectedLatitude = "\(self.currentSelectedLocation.coordinate.latitude)"
                            mainScreenUv.ufxSelectedLongitude = "\(self.currentSelectedLocation.coordinate.longitude)"
                            mainScreenUv.ufxServiceItemDict = self.ufxServiceItemDict
                            self.pushToNavController(uv: mainScreenUv)
                        }
                       
                    }else{
                        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "Address added successfully.", key: "LBL_ADDRSS_ADD_SUCCESS"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: "", completionHandler: { (btnClickedId) in
                            self.performSegue(withIdentifier: "unwindToChooseAddress", sender: self)
                        })
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
}
