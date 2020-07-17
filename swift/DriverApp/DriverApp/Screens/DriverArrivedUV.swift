//
//  DriverArrivedUV.swift
//  DriverApp
//
//  Created by NEW MAC on 26/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps
import CoreLocation

class DriverArrivedUV: UIViewController, GMSMapViewDelegate, OnLocationUpdateDelegate, OnTripCanceledDelegate, MyBtnClickDelegate {

    
    var MENU_USER_OR_DELIVERY_DETAIL = "0"
    var MENU_CANCEL_TRIP_OR_DELIVERY = "1"
    var MENU_SPECIAL_INS = "2"
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var googleMapContainerView: UIView!
//    @IBOutlet weak var navigateView: UIView!
//    @IBOutlet weak var navigateViewHeight: NSLayoutConstraint!
    
    @IBOutlet weak var topDataContainerStkView: UIStackView!
    @IBOutlet weak var topDataContainerViewHeight: NSLayoutConstraint!
    
    @IBOutlet weak var arrivedBtn: MyButton!
    @IBOutlet weak var emeImgView: UIImageView!
    
    
    let generalFunc = GeneralFunctions()
    
    var isPageLoaded = false
    
    var currentLocation:CLLocation!
    var currentRotatedLocation:CLLocation!
    var currentHeading:Double = 0
    var isFirstHeadingCompleted = false
    
    var gMapView:GMSMapView!
    
//    var navView:UIView!
    var topNavView:navigationVIew!
    
    var window:UIWindow!
    var configPubNub:ConfigPubNub?
    
    var getLocation:GetLocation!
    
    var isFirstLocationUpdate = true
    
    var tripData:NSDictionary!
    
    
    var menu:BTNavigationDropdownMenu!
    
    var updateDriverLoc:UpdateDriverLocations!
    
    var updateDirections:UpdateDirections!
    
    let driverMarker: GMSMarker = GMSMarker()
    let passengerMarker: GMSMarker = GMSMarker()
    
    var locationDialog:OpenLocationEnableView!
    
    var userProfileJson:NSDictionary!
//    var locationArr = [CLLocation]()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewWillDisappear(_ animated: Bool) {
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            
            isPageLoaded = true
            
            topNavView = navigationVIew(frame: CGRect(x:0, y:0, width: Application.screenSize.width, height: 95))
            topNavView.backgroundColor = UIColor.clear
            
            topDataContainerStkView.addArrangedSubview(topNavView)
            topDataContainerViewHeight.constant = 95
            
            let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: 0.0)
            gMapView = GMSMapView.map(withFrame: self.googleMapContainerView.frame, camera: camera)
            //        googleMapContainerView = gMapView
            //        gMapView = GMSMapView()
//            gMapView.isMyLocationEnabled = true
            gMapView.settings.rotateGestures = false
            gMapView.settings.tiltGestures = false
            gMapView.delegate = self
            self.googleMapContainerView.addSubview(gMapView)
            setData()
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        window = Application.window!
        
        Utils.driverMarkersPositionList.removeAll()
        Utils.driverMarkerAnimFinished = true
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "DriverArrivedScreenDesign", uv: self, contentView: contentView))
        
        self.emeImgView.isHidden = true
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        if(GeneralFunctions.getValue(key: "OPEN_MSG_SCREEN") != nil && (GeneralFunctions.getValue(key: "OPEN_MSG_SCREEN") as! String) == "true"){
            let chatUV = GeneralFunctions.instantiateViewController(pageName: "ChatUV") as! ChatUV
            
            GeneralFunctions.removeValue(key: "OPEN_MSG_SCREEN")
            
            chatUV.receiverId = tripData!.get("PassengerId")
            chatUV.receiverDisplayName = self.tripData!.get("PName")
            chatUV.assignedtripId = self.tripData!.get("TripId")
            self.pushToNavController(uv:chatUV, isDirect: true)
            
        }
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
        
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.502510)]
//        locationArr += [CLLocation(latitude: 23.011793, longitude: 72.502526)]
//        locationArr += [CLLocation(latitude: 23.011793, longitude: 72.502543)]
//        locationArr += [CLLocation(latitude: 23.011793, longitude: 72.502560)]
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.502579)]
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.502599)]
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.502622)]
//        locationArr += [CLLocation(latitude: 23.011791, longitude: 72.502636)]
//        locationArr += [CLLocation(latitude: 23.011811, longitude: 72.50264 )]
//        locationArr += [CLLocation(latitude: 23.011831, longitude: 72.502654)]
//        locationArr += [CLLocation(latitude: 23.011852, longitude: 72.502658)]
//        locationArr += [CLLocation(latitude: 23.011851, longitude: 72.502671)]
//        locationArr += [CLLocation(latitude: 23.011848, longitude: 72.502685)]
//        locationArr += [CLLocation(latitude: 23.011846, longitude: 72.502694)]
//        locationArr += [CLLocation(latitude: 23.011827, longitude: 72.502703)]
//        locationArr += [CLLocation(latitude: 23.011814, longitude: 72.502697)]
//        locationArr += [CLLocation(latitude: 23.011799, longitude: 72.502698)]
//        locationArr += [CLLocation(latitude: 23.011799, longitude: 72.502716)]
//        locationArr += [CLLocation(latitude: 23.011798, longitude: 72.502733)]
//        locationArr += [CLLocation(latitude: 23.011795, longitude: 72.502749)]
//        locationArr += [CLLocation(latitude: 23.011795, longitude: 72.502764)]
//        locationArr += [CLLocation(latitude: 23.011796, longitude: 72.502780)]
//        locationArr += [CLLocation(latitude: 23.011796, longitude: 72.502797)]
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.502811)]
//        locationArr += [CLLocation(latitude: 23.011798, longitude: 72.502824)]
//        locationArr += [CLLocation(latitude: 23.011795, longitude: 72.502842)]
//        locationArr += [CLLocation(latitude: 23.011796, longitude: 72.502859)]
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.502879)]
//        locationArr += [CLLocation(latitude: 23.011814, longitude: 72.502883)]
//        locationArr += [CLLocation(latitude: 23.011831, longitude: 72.502890)]
//        locationArr += [CLLocation(latitude: 23.011856, longitude: 72.502899)]
//        locationArr += [CLLocation(latitude: 23.011853, longitude: 72.502914)]
//        locationArr += [CLLocation(latitude: 23.011852, longitude: 72.502930)]
//        locationArr += [CLLocation(latitude: 23.011841, longitude: 72.502943)]
//        locationArr += [CLLocation(latitude: 23.011820, longitude: 72.502937)]
//        locationArr += [CLLocation(latitude: 23.011801, longitude: 72.502935)]
//        locationArr += [CLLocation(latitude: 23.011784, longitude: 72.502933)]
//        locationArr += [CLLocation(latitude: 23.011789, longitude: 72.502953)]
//        locationArr += [CLLocation(latitude: 23.011790, longitude: 72.502969)]
//        locationArr += [CLLocation(latitude: 23.011793, longitude: 72.502988)]
//        locationArr += [CLLocation(latitude: 23.011799, longitude: 72.503004)]
//        locationArr += [CLLocation(latitude: 23.011796, longitude: 72.503020)]
//        locationArr += [CLLocation(latitude: 23.011791, longitude: 72.503039)]
//        locationArr += [CLLocation(latitude: 23.011791, longitude: 72.503056)]
//        locationArr += [CLLocation(latitude: 23.011793, longitude: 72.503080)]
//        locationArr += [CLLocation(latitude: 23.011798, longitude: 72.503110)]
//        locationArr += [CLLocation(latitude: 23.011775, longitude: 72.503135)]
//        locationArr += [CLLocation(latitude: 23.011758, longitude: 72.503145)]
//        locationArr += [CLLocation(latitude: 23.011756, longitude: 72.503167)]
//        locationArr += [CLLocation(latitude: 23.011777, longitude: 72.503190)]
//        locationArr += [CLLocation(latitude: 23.011803, longitude: 72.503192)]
//        locationArr += [CLLocation(latitude: 23.011800, longitude: 72.503162)]
//        locationArr += [CLLocation(latitude: 23.011794, longitude: 72.503126)]
//        locationArr += [CLLocation(latitude: 23.011795, longitude: 72.503091)]
//        locationArr += [CLLocation(latitude: 23.011753, longitude: 72.503096)]
//        locationArr += [CLLocation(latitude: 23.011750, longitude: 72.503133)]
//        locationArr += [CLLocation(latitude: 23.011753, longitude: 72.503186)]
//        locationArr += [CLLocation(latitude: 23.011758, longitude: 72.503239)]
//        locationArr += [CLLocation(latitude: 23.011764, longitude: 72.503303)]
//        locationArr += [CLLocation(latitude: 23.011793, longitude: 72.503342)]
//        
//        scheduleLocUpdates()
        
    }
//    var locCount = 0
//    
//    func scheduleLocUpdates(){
//        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(2 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//            Utils.printLog(msgData: "Location Task Called")
//            
//            self.onLocationUpdate(location: self.locationArr[self.locCount])
//            self.locCount = self.locCount + 1
//            
//            if(self.locCount < self.locationArr.count){
//                self.scheduleLocUpdates()
//            }
//            
//        })
//    }
    

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        
    }
    
    deinit {
        releaseAllTask()
    }

    func setData(){
        if(self.tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PICKUP_DELIVERY")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PICKUP_DELIVERY")
        }else{
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PICK_UP_PASSENGER")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PICK_UP_PASSENGER")
        }
        
        let rightButton = UIBarButtonItem(image: UIImage(named: "ic_menu")!, style: UIBarButtonItemStyle.plain, target: self, action: #selector(self.openPopUpMenu))
        self.navigationItem.rightBarButtonItem = rightButton
        
        self.arrivedBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_ARRIVED_TXT"))
        
        self.arrivedBtn.clickDelegate = self
        
        if(self.getPubNubConfig().uppercased() == "YES"){
            configPubNub = ConfigPubNub()
            configPubNub!.iTripId = self.tripData.get("TripId")
            configPubNub!.buildPubNub()
        }else{
            
            self.updateDriverLoc = UpdateDriverLocations(uv: self)
            self.updateDriverLoc.scheduleDriverLocUpdate()
        }
        
        
        self.getLocation = GetLocation(uv: self, isContinuous: true)
        self.getLocation.buildLocManager(locationUpdateDelegate: self)
        
        if(self.topNavView != nil){
            self.topNavView.navigateLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NAVIGATE")
            self.topNavView.navOptionView.backgroundColor = UIColor.UCAColor.AppThemeColor_1
            //        self.navView.subviews[0].subviews[1].subviews[0].backgroundColor = UIColor(hex: 0xFFFFFF)
            GeneralFunctions.setImgTintColor(imgView: self.topNavView.navImgView, color: UIColor.UCAColor.AppThemeTxtColor_1)
            self.topNavView.navigateLbl.textColor = UIColor.UCAColor.AppThemeTxtColor_1
            
            let navViewTapGue = UITapGestureRecognizer()
            navViewTapGue.addTarget(self, action: #selector(self.navViewTapped))
            self.topNavView.navOptionView.isUserInteractionEnabled = true
            self.topNavView.navOptionView.addGestureRecognizer(navViewTapGue)
        }
        
        self.observeCancelTripRequest()
        initializeMenu()
        
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)
        
        let passengerLocation = CLLocation(latitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripData!.get("sourceLatitude")), longitude: GeneralFunctions.parseDouble(origValue: 0.0, data: tripData!.get("sourceLongitude")))
        updateDirections = UpdateDirections(uv: self, gMap: gMapView, destinationLocation: passengerLocation, navigateView: topNavView)
        updateDirections.scheduleDirectionUpdate()
        
        self.addPassengerMarker(location: passengerLocation)
        
        self.emeImgView.isUserInteractionEnabled = true
        
        self.emeImgView.isHidden = false
        let emeTapGue = UITapGestureRecognizer()
        emeTapGue.addTarget(self, action: #selector(self.emeImgViewTapped))
        self.emeImgView.addGestureRecognizer(emeTapGue)
        
        checkLocationEnabled()
        
        addBackgroundObserver()
        
        
    }
    
    func mapView(_ mapView: GMSMapView, didTap marker: GMSMarker) -> Bool {
        mapView.selectedMarker = nil
        return true
    }
    func addBackgroundObserver(){
        NotificationCenter.default.removeObserver(self, name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
        NotificationCenter.default.addObserver(self, selector: #selector(self.appInForground), name: NSNotification.Name(rawValue: Utils.appFGNotificationKey), object: nil)
    }
    
    func checkLocationEnabled(){
        if(locationDialog != nil){
            locationDialog.removeView()
            locationDialog = nil
        }
        
        if(GeneralFunctions.hasLocationEnabled() == false || InternetConnection.isConnectedToNetwork() == false){
            
            locationDialog = OpenLocationEnableView(uv: self, containerView: self.contentView, gMapView: self.gMapView, isMapLocEnabled: false)
            locationDialog.show()
            
            return
        }
    }
    
    func appInForground(){
        checkLocationEnabled()
        
        if(self.configPubNub != nil){
            self.configPubNub!.unSubscribeToPrivateChannel()
            self.configPubNub!.subscribeToPrivateChannel()
        }
    }
    
    func emeImgViewTapped(){
        let confirmEmergencyTapUV = GeneralFunctions.instantiateViewController(pageName: "ConfirmEmergencyTapUV") as! ConfirmEmergencyTapUV
        confirmEmergencyTapUV.iTripId = tripData.get("TripId")
        self.pushToNavController(uv: confirmEmergencyTapUV)
    }
    
    func navViewTapped(){
        let openNavOption = OpenNavOption(uv: self, containerView: self.view, placeLatitude: tripData!.get("sourceLatitude"), placeLongitude: tripData!.get("sourceLongitude"))
        openNavOption.chooseOption()
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.arrivedBtn){
            if(self.currentLocation == nil){
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_NO_LOCATION_FOUND_TXT"))
                return
            }
            self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ARRIVED_CONFIRM_DIALOG_TXT"), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT"), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CANCEL_TXT"), completionHandler: { (btnClickedId) in
                
                if(btnClickedId == 0){
                    self.setDriverStatusToArrived()
                }else{
                }
            })
        }
    }

    func setDriverStatusToArrived(){
        let parameters = ["type":"DriverArrived","iTripId":self.tripData!.get("TripId"),"iDriverId": GeneralFunctions.getMemberd(),"vLatitude":"\(self.currentLocation != nil ? "\(currentLocation.coordinate.latitude)" : "")","vLongitude":"\(self.currentLocation != nil ? "\(currentLocation.coordinate.longitude)" : "")"]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.releaseAllTask()
                    
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else if(dataDict.get(Utils.message_str) == "DO_RESTART" || dataDict.get("message") == "LBL_SERVER_COMM_ERROR" || dataDict.get("message") == "GCM_FAILED" || dataDict.get("message") == "APNS_FAILED"){
                    
                    self.releaseAllTask()
                    
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func releaseAllTask(isDismiss:Bool = true){
        
        if(gMapView != nil){
            gMapView!.stopRendering()
            gMapView!.removeFromSuperview()
            gMapView!.clear()
            gMapView!.delegate = nil
            gMapView = nil
        }
        
        if(configPubNub != nil){
            configPubNub!.releasePubNub()
        }
        
        if(self.getLocation != nil){
            self.getLocation!.locationUpdateDelegate = nil
            self.getLocation!.releaseLocationTask()
            self.getLocation = nil
        }
        
        
        if(updateDriverLoc != nil){
            self.updateDriverLoc.releaseTask()
            self.updateDriverLoc = nil
        }
        
        if(updateDirections != nil){
            self.updateDirections.releaseTask()
            if(self.updateDirections.gMap != nil){
                self.updateDirections.gMap!.stopRendering()
                self.updateDirections.gMap!.removeFromSuperview()
                self.updateDirections.gMap!.clear()
                self.updateDirections.gMap!.delegate = nil
                self.updateDirections.gMap = nil
            }
            
            
            if(updateDirections.navigateView != nil){
                updateDirections.navigateView = nil
            }
            
            self.updateDirections = nil
        }
        
        GeneralFunctions.removeObserver(obj: self)
        
        
        if(isDismiss){
            self.dismiss(animated: false, completion: nil)
            self.navigationController?.dismiss(animated: false, completion: nil)
        }
    }
    
    
    func onHeadingUpdate(heading: Double) {
//        driverMarker.isFlat = true
//        driverMarker.rotation = heading
//       
//        self.gMapView.animate(toBearing: heading - 20)
        currentHeading = heading
        
        if(isFirstHeadingCompleted == false){
            updateDriverMarker()
            isFirstHeadingCompleted = true
        }
    }
    
    
    func onLocationUpdate(location: CLLocation) {
        if(gMapView == nil){
            releaseAllTask()
            return
        }
        
        self.currentLocation = location
        
        var currentZoomLevel:Float = self.gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel && isFirstLocationUpdate == true){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
                                              longitude: location.coordinate.longitude, zoom: currentZoomLevel)
        
        self.gMapView.animate(to: camera)
        
        isFirstLocationUpdate = false
        
        updateLocationToPubNub()
        
        updateDriverMarker()
        
    }
    

    
    func updateDriverMarker(){
//        driverMarker.position = self.currentLocation.coordinate
        if(currentLocation == nil){
            return
        }
        driverMarker.title = GeneralFunctions.getMemberd()
        
        var rotationAngle:Double = 0
        if(currentRotatedLocation == nil){
            rotationAngle = currentHeading
            
            if(currentHeading > 1){
                currentRotatedLocation = currentLocation
            }
        }else{
            rotationAngle = currentRotatedLocation.bearingToLocationDegrees(destinationLocation: currentLocation, currentRotation: driverMarker.rotation)
            if(rotationAngle == -1){
                rotationAngle = currentHeading
            }else{
                currentRotatedLocation = currentLocation
            }
        }
        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            rotationAngle = 0
        }
        
//        Utils.updateMarker(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 1.0)
        
//        if(dataDict != nil && self.driverMarker != nil){
        
            let previousItemOfMarker = Utils.getLastLocationDataOfMarker(marker: driverMarker)
            
            var tempData = [String:String]()
            tempData["vLatitude"] = "\(currentLocation.coordinate.latitude)"
            tempData["vLongitude"] = "\(currentLocation.coordinate.longitude)"
            tempData["iDriverId"] = "\(GeneralFunctions.getMemberd())"
            tempData["RotationAngle"] = "\(rotationAngle)"
            tempData["LocTime"] = "\(Utils.currentTimeMillis())"
            
            if(previousItemOfMarker.get("LocTime") != "" && (tempData as NSDictionary).get("LocTime") != ""){
                
                let locTime = Int64(previousItemOfMarker.get("LocTime"))
                let newLocTime = Int64((tempData as NSDictionary).get("LocTime"))
                
                if(locTime != nil && newLocTime != nil){
                    
                    if((newLocTime! - locTime!) > 0 && Utils.driverMarkerAnimFinished == false){
                        Utils.driverMarkersPositionList.append(tempData as NSDictionary)
                    }else if((newLocTime! - locTime!) > 0){
                        Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: GeneralFunctions.getMemberd(), LocTime: (tempData as NSDictionary).get("LocTime"))
                    }
                    
                }else if((locTime == nil || newLocTime == nil) && Utils.driverMarkerAnimFinished == false){
                    Utils.driverMarkersPositionList.append(tempData as NSDictionary)
                }else{
                    Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: GeneralFunctions.getMemberd(), LocTime: (tempData as NSDictionary).get("LocTime"))
                }
                
            }else if(Utils.driverMarkerAnimFinished == false){
                Utils.driverMarkersPositionList.append(tempData as NSDictionary)
            }else{
                Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 0.8, iDriverId: GeneralFunctions.getMemberd(), LocTime: (tempData as NSDictionary).get("LocTime"))
            }
            
//        }else{
//            Utils.updateMarkerOnTrip(marker: driverMarker, googleMap: self.gMapView, coordinates: currentLocation.coordinate, rotationAngle: rotationAngle, duration: 1.0, iDriverId: GeneralFunctions.getMemberd(), LocTime: "")
//        }

        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            let providerView = self.getProviderMarkerView(providerImage: UIImage(named: "ic_no_pic_user")!)
            driverMarker.icon = UIImage(view: providerView)
            
            (providerView.subviews[1] as! UIImageView).sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImage")), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
                self.driverMarker.icon = UIImage(view: providerView)
            })
            driverMarker.groundAnchor = CGPoint(x: 0.5, y: 1.0)
        }else{
	
            let eIconType = tripData.get("eIconType")
               // driverMarker.icon = UIImage(named: "ic_driver_car_pin")
            var iconId = "ic_driver_car_pin"
            
            if(eIconType == "Bike"){
                iconId = "ic_bike"
            }else if(eIconType == "Cycle"){
                iconId = "ic_cycle"
            }else if(eIconType == "Truck"){
                iconId = "ic_truck"
            }
            
            driverMarker.icon = UIImage(named: iconId)
            driverMarker.groundAnchor = CGPoint(x: 0.5, y: 0.5)
        }
        driverMarker.map = self.gMapView
        driverMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
        driverMarker.isFlat = true
        driverMarker.title = GeneralFunctions.getMemberd()
        
        var currentZoomLevel:Float = gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        let camera = GMSCameraPosition.camera(withLatitude: self.currentLocation.coordinate.latitude,
                                                          longitude: self.currentLocation.coordinate.longitude, zoom: currentZoomLevel)
        
        self.gMapView.animate(to: camera)
    }
    
    func addPassengerMarker(location: CLLocation){
        
        passengerMarker.position = location.coordinate
        
        if(self.tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_Deliver.uppercased()){
            passengerMarker.icon = UIImage(named: "ic_sender")
        }else if(self.tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            passengerMarker.icon = UIImage(named: "ic_user")
        }else{
            passengerMarker.icon = UIImage(named: "ic_passenger")
        }
        
//        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
//            passengerMarker.icon = UIImage(named: "ic_user")
//        }else{
//            passengerMarker.icon = UIImage(named: "ic_passenger")
//        }
        passengerMarker.map = self.gMapView
        passengerMarker.infoWindowAnchor = CGPoint(x: 0.5, y: 0.5)
        passengerMarker.groundAnchor = CGPoint(x: 0.5, y: 1.0)
        
    }
    
    func updateLocationToPubNub(){
        if(currentLocation != nil){
            configPubNub?.publishMsg(channelName: GeneralFunctions.getLocationUpdateChannel(), content: GeneralFunctions.buildLocationJson(location: currentLocation!, msgType: "LocationUpdateOnTrip"))
        }
    }
    
    func initializeMenu(){
        
        var items = [NSDictionary]()
        
        if(self.tripData!.get("eHailTrip").uppercased() != "YES"){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_VIEW_DELIVERY_DETAILS" : "LBL_VIEW_PASSENGER_DETAIL"),"ID" : MENU_USER_OR_DELIVERY_DETAIL] as NSDictionary)
        }
        
        
        
        if(tripData!.get("REQUEST_TYPE").uppercased() == Utils.cabGeneralType_UberX.uppercased()){
            items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "Special Instruction", key: "LBL_SPECIAL_INSTRUCTION_TXT"),"ID" : MENU_SPECIAL_INS] as NSDictionary)
        }
        items.append(["Title" : self.generalFunc.getLanguageLabel(origValue: "", key: tripData!.get("REQUEST_TYPE") == Utils.cabGeneralType_Deliver ? "LBL_CANCEL_DELIVERY" : "LBL_CANCEL_TRIP"),"ID" : MENU_CANCEL_TRIP_OR_DELIVERY] as NSDictionary)
        
        if(self.menu == nil){
            menu = BTNavigationDropdownMenu(navigationController: self.navigationController, title: "", items: items)
            
            menu.cellHeight = 65
            menu.cellBackgroundColor = UIColor.UCAColor.AppThemeColor.lighter(by: 10)
            menu.cellSelectionColor = UIColor.UCAColor.AppThemeColor
            menu.cellTextLabelColor = UIColor.UCAColor.AppThemeTxtColor
            menu.cellTextLabelFont = UIFont(name: "Roboto-Light", size: 20)
            menu.cellSeparatorColor = UIColor.UCAColor.AppThemeColor
            
            if(Configurations.isRTLMode()){
                menu.cellTextLabelAlignment = NSTextAlignment.right
            }else{
                menu.cellTextLabelAlignment = NSTextAlignment.left
            }
            menu.arrowPadding = 15
            menu.animationDuration = 0.5
            menu.maskBackgroundColor = UIColor.black
            menu.maskBackgroundOpacity = 0.5
            menu.menuStateHandler = { (isMenuOpen: Bool) -> () in
                
                //                if(isMenuOpen){
                //                    self.rightButton.setBackgroundImage(nil, for: .normal, barMetrics: .default)
                //
                //                }else{
                //                    self.rightButton.setBackgroundImage(UIImage(color : UIColor.UCAColor.AppThemeColor.lighter(by: 10)!), for: .normal, barMetrics: .default)
                //                }
                
            }
            menu.didSelectItemAtIndexHandler = {(indexID: String) -> () in
                
                switch indexID {
                    
                case self.MENU_USER_OR_DELIVERY_DETAIL:
                  
                        let openPassengerDetail = OpenPassengerDetail(uv:self, containerView: self.contentView)
                        openPassengerDetail.tripData = self.tripData
                        openPassengerDetail.currInst = openPassengerDetail
                        openPassengerDetail.showDetail()
                     
                    break
                case self.MENU_CANCEL_TRIP_OR_DELIVERY:
                    let openCancelTrip = OpenCancelTrip(uv:self, containerView: self.contentView)
                    openCancelTrip.tripData = self.tripData
                    openCancelTrip.currInst = openCancelTrip
                    
                    openCancelTrip.setDelegate(onTripCanceledDelegate: self)
                    openCancelTrip.cancelTrip()
                    break
                
                case self.MENU_SPECIAL_INS:
                    self.generalFunc.setError(uv: self, title: self.generalFunc.getLanguageLabel(origValue: "Special Instruction", key: "LBL_SPECIAL_INSTRUCTION_TXT"), content: self.tripData!.get("tUserComment") == "" ? (self.generalFunc.getLanguageLabel(origValue: "There is a No Special Instruction", key: "LBL_NO_SPECIAL_INSTRUCTION")) : self.tripData!.get("tUserComment") )
                    break
                default:
                    break
                }
                
            }
        }else{
            menu.updateItems(items)
        }
    }
    
    func openPopUpMenu(){
        
        initializeMenu()
        
        if(menu.isShown){
            menu.hideMenu()
            return
        }else{
            menu.showMenu()
        }
    }
    
    func onTripViewClosed(openCancelTrip:OpenCancelTrip) {
        openCancelTrip.setDelegate(onTripCanceledDelegate: nil)
    }
    
    func onTripCanceled(reason: String, comment: String, openCancelTrip:OpenCancelTrip) {
        openCancelTrip.setDelegate(onTripCanceledDelegate: nil)
        
        let parameters = ["type":"cancelTrip","iDriverId": GeneralFunctions.getMemberd(), "iUserId": tripData!.get("PassengerId"), "iTripId": tripData!.get("TripId"), "UserType": Utils.appUserType, "Reason": reason, "Comment": comment]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.releaseAllTask()
                    
                    let window = Application.window
                    
                    let getUserData = GetUserData(uv: self, window: window!)
                    getUserData.getdata()
                    
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    func getProviderMarkerView(providerImage:UIImage) -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "ProviderMapMarkerView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        view.frame.size = CGSize(width: 64, height: 100)
        
        GeneralFunctions.setImgTintColor(imgView: view.subviews[0] as! UIImageView, color: UIColor.UCAColor.AppThemeColor)
        
        view.subviews[1].layer.cornerRadius = view.subviews[1].frame.width / 2
        view.subviews[1].layer.masksToBounds = true
        let providerImgView = view.subviews[1] as! UIImageView
        providerImgView.image = providerImage
        
        return view
    }
}
