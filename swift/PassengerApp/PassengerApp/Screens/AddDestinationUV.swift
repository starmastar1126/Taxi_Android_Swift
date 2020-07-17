//
//  AddDestinationUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 31/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps
import CoreLocation

class AddDestinationUV: UIViewController, GMSMapViewDelegate, OnLocationUpdateDelegate, AddressFoundDelegate, MyBtnClickDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var googleMapContainerView: UIView!
    @IBOutlet weak var addLocationBtn: MyButton!
    @IBOutlet weak var locAreaView: UIView!
    @IBOutlet weak var locLbl: MyLabel!
    @IBOutlet weak var selectLocImgView: UIImageView!
    
    var centerLocation:CLLocation!
    
    var SCREEN_TYPE = "DESTINATION"
    
    let generalFunc = GeneralFunctions()
    
    var isFirstLocationUpdate = true
    var isPageLoaded = false
    var gMapView:GMSMapView!
    
    var isFromRecentLocView = false
    var isFromSearchPlaces = false
    
    var isFromSelectLoc = false
    var isFromMainScreen = false
    
    var getLocation:GetLocation!
    
    var getAddressFrmLocation:GetAddressFromLocation!
    
    var selectedLocation:CLLocation!
    var selectedAddress = ""
    
    let placeMarker: GMSMarker = GMSMarker()
    
//    var isSkipCurrentChange = false
    var isSelectingLocation = false
    var isSkipMapLocSelectOnChangeCamera = false
    var isSkipCurrentMoveOnAddress = false
    
    var iUserFavAddressId = ""
    var userProfileJson:NSDictionary!
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "AddDestinationScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let userFavouriteAddressArr = userProfileJson.getArrObj("UserFavouriteAddress")
        if(userFavouriteAddressArr.count > 0){
            for i in 0..<userFavouriteAddressArr.count {
                let dataItem = userFavouriteAddressArr[i] as! NSDictionary
                if(dataItem.get("eType").uppercased() == self.SCREEN_TYPE.uppercased()){
                    self.iUserFavAddressId = dataItem.get("iUserFavAddressId")
                }
            }
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            
            isPageLoaded = true
            
            let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: 0.0)
//            gMapView = GMSMapView.map(withFrame: self.googleMapContainerView.frame, camera: camera)
            gMapView = GMSMapView.map(withFrame: CGRect(x: 0, y:0, width: self.googleMapContainerView.frame.size.width, height: self.googleMapContainerView.frame.size.height), camera: camera)
            //        googleMapContainerView = gMapView
            //        gMapView = GMSMapView()
            //            gMapView.isMyLocationEnabled = true
            gMapView.delegate = self
            self.googleMapContainerView.addSubview(gMapView)
            
            setData()
            
        }
    }
    
    func setData(){
        if(SCREEN_TYPE == "DESTINATION"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_DESTINATION_HEADER_TXT")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECT_DESTINATION_HEADER_TXT")
        }else if(SCREEN_TYPE == "PICKUP"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SET_PICK_UP_LOCATION_TXT")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SET_PICK_UP_LOCATION_TXT")
        }else if(SCREEN_TYPE == "HOME"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_HOME_BIG_TXT")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_HOME_BIG_TXT")
        }else if(SCREEN_TYPE == "WORK"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_WORK_HEADER_TXT")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_WORK_HEADER_TXT")
        }else{
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_LOC")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_LOC")
        }
        
        self.locLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SEARCH_PLACE_HINT_TXT")
        
        self.addLocationBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_LOC"))
        self.addLocationBtn.clickDelegate = self
        //        self.addLocationBtn.unwindToActiveTrip
        
        getAddressFrmLocation = GetAddressFromLocation(uv: self, addressFoundDelegate: self)
        
        if(centerLocation == nil){
            self.getLocation = GetLocation(uv: self, isContinuous: true)
            self.getLocation.buildLocManager(locationUpdateDelegate: self)
        }else{
            isSkipCurrentMoveOnAddress = true
            self.locLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
            
            getAddressFrmLocation.setLocation(latitude: centerLocation!.coordinate.latitude, longitude: centerLocation!.coordinate.longitude)
            getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow: true)
            
            isSkipMapLocSelectOnChangeCamera = true
            self.animateGmapCamera(location: centerLocation!, zoomLevel: Utils.defaultZoomLevel)
        }
        
//        Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_LONG_TOUCH_CHANGE_LOC_TXT"), uv: self)
        
        let placeTapGue = UITapGestureRecognizer()
        placeTapGue.addTarget(self, action: #selector(self.launchPlaceFinder))
        
        locAreaView.isUserInteractionEnabled = true
        locAreaView.addGestureRecognizer(placeTapGue)
        
        GeneralFunctions.setImgTintColor(imgView: selectLocImgView, color: UIColor.UCAColor.AppThemeColor_1)
        
        self.selectLocImgView.isHidden = false
    }
    
    func launchPlaceFinder(){
        let launchPlaceFinder = LaunchPlaceFinder(viewControllerUV: self)
        launchPlaceFinder.currInst = launchPlaceFinder
        launchPlaceFinder.isFromSelectLoc = true
        if(centerLocation != nil){
            launchPlaceFinder.setBiasLocation(sourceLocationPlaceLatitude: centerLocation!.coordinate.latitude, sourceLocationPlaceLongitude: centerLocation!.coordinate.longitude)
        }
        
        launchPlaceFinder.initializeFinder { (address, latitude, longitude) in
            self.locLbl.text = address
            self.selectedAddress = address
            self.selectedLocation = CLLocation(latitude: latitude, longitude: longitude)
            
            self.isSelectingLocation = false
            
            self.isSkipMapLocSelectOnChangeCamera = true
            
            self.changeMarkerPosition(location: self.selectedLocation, zoomLevel: Utils.defaultZoomLevel)
        }
    }
    
    override func closeCurrentScreen() {
        releaseAllTask()
        super.closeCurrentScreen()
    }
    deinit {
        releaseAllTask()
    }
    
    func releaseAllTask(isDismiss:Bool = true){
        
        if(gMapView != nil){
            gMapView!.stopRendering()
            gMapView!.removeFromSuperview()
            gMapView!.clear()
            gMapView!.delegate = nil
            gMapView = nil
        }
        
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        if(getAddressFrmLocation != nil){
            getAddressFrmLocation!.addressFoundDelegate = nil
            getAddressFrmLocation = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
        
        
        if(isDismiss){
//            self.dismiss(animated: false, completion: nil)
//            self.navigationController?.dismiss(animated: false, completion: nil)
        }
    }
    
    func mapView(_ mapView: GMSMapView, didLongPressAt coordinate: CLLocationCoordinate2D) {
//        getAddressFrmLocation.setLocation(latitude: coordinate.latitude, longitude: coordinate.longitude)
//        getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow: true)
        
//        self.animateGmapCamera(location: CLLocation(latitude: coordinate.latitude, longitude: coordinate.longitude))
    }
    
    func onLocationUpdate(location: CLLocation) {
        if(gMapView == nil){
            releaseAllTask()
            return
        }
        if(isFirstLocationUpdate == true){
            
            
            isSkipCurrentMoveOnAddress = true
            isSkipMapLocSelectOnChangeCamera = true
            
            self.animateGmapCamera(location: location, zoomLevel: Utils.defaultZoomLevel)
            
            getAddressFrmLocation.setLocation(latitude: location.coordinate.latitude, longitude: location.coordinate.longitude)
            getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow: true)
            
            self.locLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
            
        }
        
        
        isFirstLocationUpdate = false
    }
    
    func onAddressFound(address: String, location:CLLocation, isPickUpMode:Bool, dataResult:String) {
        if(address == ""){
            return
        }
        self.locLbl.text = address
        self.selectedAddress = address
        self.selectedLocation = location
        
        self.isSelectingLocation = false
        
        if(isSkipCurrentMoveOnAddress == true){
            isSkipCurrentMoveOnAddress = false
            return
        }
        
        if(getCenterLocation().coordinate.latitude != location.coordinate.latitude || getCenterLocation().coordinate.longitude != location.coordinate.longitude){
            isSkipMapLocSelectOnChangeCamera = true
        }
        
        changeMarkerPosition(location: location, zoomLevel: self.gMapView.camera.zoom)
    }
    
    
    
    func mapView(_ mapView: GMSMapView, idleAt position: GMSCameraPosition) {
        if(isSkipMapLocSelectOnChangeCamera == true){
            isSkipMapLocSelectOnChangeCamera = false
            return
        }
        self.locLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
            
        self.isSelectingLocation = true

        getAddressFrmLocation.setLocation(latitude: getCenterLocation().coordinate.latitude, longitude: getCenterLocation().coordinate.longitude)
        getAddressFrmLocation.executeProcess(isOpenLoader: true, isAlertShow: false)
        
    }
    
    func getCenterLocation() -> CLLocation{
        return CLLocation(latitude: self.gMapView.camera.target.latitude, longitude: self.gMapView.camera.target.longitude)
    }
    
    func changeMarkerPosition(location:CLLocation, zoomLevel:Float){
        placeMarker.position = location.coordinate
        
        placeMarker.icon = UIImage(named: "ic_destination_place_image")
//        placeMarker.map = self.gMapView
        placeMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
        
        self.animateGmapCamera(location: location, zoomLevel: zoomLevel)
        
    }
    
    
    func animateGmapCamera(location:CLLocation, zoomLevel:Float){
        if(self.gMapView == nil){
            return
        }

        var currentZoomLevel:Float = zoomLevel
        
        if(isFirstLocationUpdate == true){
            currentZoomLevel = Utils.defaultZoomLevel
            isFirstLocationUpdate = false
        }
        
        let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
                                              longitude: location.coordinate.longitude, zoom: currentZoomLevel)
        
//        if(isSkipCurrentChange == false){
            self.gMapView.moveCamera(GMSCameraUpdate.setCamera(camera))
//        }else{
//            isSkipCurrentChange = false
//        }
//        self.gMapView.animate(to: camera)
    }

    func addToServer(vAddress:String, vLatitude:String, vLongitude:String, eType:String){
        let parameters = ["type":"UpdateUserFavouriteAddress","iUserId": GeneralFunctions.getMemberd(), "eUserType": Utils.appUserType, "vAddress": vAddress, "vLatitude": vLatitude, "vLongitude": vLongitude, "eType": eType, "iUserFavAddressId": iUserFavAddressId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    _ = SetUserData(uv: self, userProfileJson: dataDict, isStoreUserId: false)

                    self.releaseAllTask()
                    if(self.isFromMainScreen == true){
                        self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                        return
                    }
                    if(self.isFromSearchPlaces == true){
                        self.performSegue(withIdentifier: "unwindToSearchPlaceScreen", sender: self)
                    }else if(self.isFromRecentLocView == false && (self.SCREEN_TYPE == "HOME" || self.SCREEN_TYPE == "WORK")){
                        self.performSegue(withIdentifier: "unwindToViewProfileScreen", sender: self)
                    }else{
                        self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                    }
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func myBtnTapped(sender: MyButton) {
        
        if(sender == self.addLocationBtn){
            if(self.selectedLocation == nil || self.isSelectingLocation == true){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SET_LOCATION"), uv: self)
            }else{
                if(self.SCREEN_TYPE == "HOME" || self.SCREEN_TYPE == "WORK"){
                    
                    self.addToServer(vAddress: "\(selectedAddress)", vLatitude: "\(selectedLocation.coordinate.latitude)", vLongitude: "\(selectedLocation.coordinate.longitude)", eType: self.SCREEN_TYPE == "HOME" ? "Home" : "Work")
                    
                    return
                }
                releaseAllTask()
                if(self.isFromMainScreen == true){
                    self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                    return
                }
                if(self.isFromSearchPlaces == true){
                    self.performSegue(withIdentifier: "unwindToSearchPlaceScreen", sender: self)
                }else if(self.isFromRecentLocView == false && (self.SCREEN_TYPE == "HOME" || self.SCREEN_TYPE == "WORK")){
                    self.performSegue(withIdentifier: "unwindToViewProfileScreen", sender: self)
                }else{
                    self.performSegue(withIdentifier: "unwindToMainScreen", sender: self)
                }
            }
        }
    }
    
}
