//
//  AddressContainerView.swift
//  PassengerApp
//
//  Created by NEW MAC on 09/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation
import GoogleMaps

class AddressContainerView: UIView {
    
    typealias CompletionHandler = (_ isPickUpMode:Bool, _ view:UIView) -> Void


    @IBOutlet weak var pickUpView: UIView!
    @IBOutlet weak var destView: UIView!
    @IBOutlet weak var pickUpViewHeight: NSLayoutConstraint!
    @IBOutlet weak var destViewHeight: NSLayoutConstraint!
    @IBOutlet weak var destViewRightMargin: NSLayoutConstraint!
    @IBOutlet weak var destViewLeftMargin: NSLayoutConstraint!
    @IBOutlet weak var pickUpViewLeftMargin: NSLayoutConstraint!
    @IBOutlet weak var pickUpViewRightMargin: NSLayoutConstraint!
//    @IBOutlet weak var pickUpLocImgView: UIImageView!
//    @IBOutlet weak var destLocImgView: UIImageView!
    @IBOutlet weak var pickUpAddressLbl: MyLabel!
    @IBOutlet weak var destAddressLbl: MyLabel!
    @IBOutlet weak var destLocHLbl: MyLabel!
    @IBOutlet weak var pickUpLocHLbl: MyLabel!
    @IBOutlet weak var pickUpPointView: UIView!
    @IBOutlet weak var destHPointView: UIView!
    @IBOutlet weak var destAddPointView: UIView!
    @IBOutlet weak var addPickUpImgView: UIImageView!
    @IBOutlet weak var addPickUpImgYPosition: NSLayoutConstraint!
    @IBOutlet weak var addDestImgView: UIImageView!
    @IBOutlet weak var addDestImgYPosition: NSLayoutConstraint!
    
    var isDriverAssigned = false
    var isDestAdded = false
    
    var mainScreenUv:MainScreenUV!
    
    var view: UIView!
    
    var handler:CompletionHandler!
    
    let generalFunc = GeneralFunctions()
    
    let pickUpAddViewTapGue = UITapGestureRecognizer()
    let destAddViewTapGue = UITapGestureRecognizer()
    
    var isPickUpMode = true
    
    var tmpPickUpPointView = UIView()
    var tmpDestPointView = UIView()
    
    init(frame: CGRect, mainScreenUv:MainScreenUV) {
        // 1. setup any properties here
        
        // 2. call super.init(frame:)
        super.init(frame: frame)
        
        self.mainScreenUv = mainScreenUv
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    required init?(coder aDecoder: NSCoder) {
        // 1. setup any properties here
        
        // 2. call super.init(coder:)
        super.init(coder: aDecoder)
        
        // 3. Setup view from .xib file
        xibSetup()
    }
    
    func setViewHandler(handler: @escaping CompletionHandler){
        self.handler = handler
    }
    
    func xibSetup() {
        view = loadViewFromNib()
        
        // use bounds not frame or it'll be offset
        view.frame = bounds
        
        // Make the view stretch with containing view
        //        view.autoresizingMask = [UIViewAutoresizing.flexibleWidth, UIViewAutoresizing.flexibleHeight]
        // Adding custom subview on top of our view (over any custom drawing > see note below)
        addSubview(view)
        
        pickUpPointView.backgroundColor = UIColor(hex: 0x1c730b)
        destHPointView.backgroundColor = UIColor.red
        destAddPointView.backgroundColor = UIColor.red
        
        destHPointView.isHidden = true
        destLocHLbl.isHidden = true
        addDestImgView.isHidden = true
        
//        GeneralFunctions.setImgTintColor(imgView: destLocImgView, color: UIColor.red)
//        GeneralFunctions.setImgTintColor(imgView: pickUpLocImgView, color: UIColor(hex: 0x1c730b))
        
        pickUpAddViewTapGue.addTarget(self, action: #selector(self.pickUpViewTappedOnAction))
        destAddViewTapGue.addTarget(self, action: #selector(self.destViewTappedOnAction))
        
        Utils.createRoundedView(view: pickUpPointView, borderColor: UIColor.clear, borderWidth: 0)
        Utils.createRoundedView(view: destHPointView, borderColor: UIColor.clear, borderWidth: 0)
        Utils.createRoundedView(view: destAddPointView, borderColor: UIColor.clear, borderWidth: 0)
        
        pickUpView.isUserInteractionEnabled = true
        pickUpView.addGestureRecognizer(pickUpAddViewTapGue)
        
        destView.isUserInteractionEnabled = true
        destView.addGestureRecognizer(destAddViewTapGue)
        
        pickUpView.masksToBounds = false
        destView.masksToBounds = false
        
        destAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ADD_DESTINATION_BTN_TXT")
        
        pickUpLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Pickup from", key: "LBL_PICK_UP_FROM")
        destLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Drop at", key: "LBL_DROP_AT")
        
        pickUpAddressLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SELECTING_LOCATION_TXT")
        
        if(mainScreenUv != nil){
            if(mainScreenUv.userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "NONE"){
                self.destView.isHidden = true
                self.destViewHeight.constant = 0
                self.frame.size = CGSize(width: self.view.frame.width, height: self.view.frame.height - 30)
                self.view.frame.size = CGSize(width: self.view.frame.width, height: self.view.frame.height - 30)
            }
            
            tmpPickUpPointView.backgroundColor = UIColor.clear
            tmpDestPointView.backgroundColor = UIColor.clear
            
            self.mainScreenUv.view.addSubview(tmpPickUpPointView)
            self.mainScreenUv.view.addSubview(tmpDestPointView)
            
        }
    }
    
    func setLocationIndicator(){
        if(self.mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == true && self.mainScreenUv.userProfileJson.get("IS_DEST_ANYTIME_CHANGE").uppercased() == "YES" && self.mainScreenUv.eTripType.uppercased() == Utils.cabGeneralType_Ride.uppercased() ){
            self.addDestImgView.isHidden = false
            Utils.printLog(msgData: "destAddress::\(self.mainScreenUv.destAddress)")
            if(self.mainScreenUv.destAddress != ""){
                self.addDestImgView.image = UIImage(named: "ic_edit")!
            }else{
                self.addDestImgView.image = UIImage(named: "ic_add_plus")!
            }
            
            if(self.isPickUpMode == true){
                self.addDestImgYPosition.constant = 10
            }else{
                self.addDestImgYPosition.constant = 0
            }
        }else if(self.mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == false){
            self.addDestImgView.isHidden = false
            if(self.mainScreenUv.destAddress == ""){
                self.addDestImgView.image = UIImage(named: "ic_add_plus")!
            }else{
                self.addDestImgView.image = UIImage(named: "ic_edit")!
            }
            
            if(self.isPickUpMode == true){
                self.addDestImgYPosition.constant = 10
            }else{
                self.addDestImgYPosition.constant = 0
            }
        }
        
        if(mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == false){
            self.addPickUpImgView.isHidden = false
            if(self.mainScreenUv.pickUpAddress != ""){
                self.addPickUpImgView.image = UIImage(named: "ic_edit")!
            }else{
                self.addPickUpImgView.image = UIImage(named: "ic_add_plus")!
            }
            
            if(self.isPickUpMode){
                addPickUpImgYPosition.constant = 0
            }else{
                addPickUpImgYPosition.constant = -10
            }
        }
    }
    
    func pickUpViewTappedOnAction(){
        pickUpTapped(isOpenSelection: true)
    }
    
    func addPickUpMarker(isMoveToPickUP:Bool){
        if(self.mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == true && self.mainScreenUv.pickUpLocation != nil){//self.mainScreenUv.isTripStarted == true
            
            if(self.mainScreenUv.isDriverArrived == true){
                if(self.mainScreenUv.pickUpPointMarker != nil){
                    self.mainScreenUv.pickUpPointMarker.map = nil
                }
                self.mainScreenUv.pickUpPointMarker = GMSMarker()
                self.mainScreenUv.pickUpPointMarker.icon = UIImage(named: "ic_source_marker")
                self.mainScreenUv.pickUpPointMarker.position = self.mainScreenUv.pickUpLocation.coordinate
                self.mainScreenUv.pickUpPointMarker.map = self.mainScreenUv.gMapView
            }
            
            if(isPickUpMode && isMoveToPickUP){
                goToPickLoc()
            }
        }
    }
    
    func pickUpTapped(isOpenSelection:Bool){
        
        mainScreenUv.sourcePinImgViewWidth.constant = 80
        mainScreenUv.sourcePinImgViewHeight.constant = 80
        mainScreenUv.sourcePinImgViewOffset.constant = -40
        
        self.addPickUpMarker(isMoveToPickUP: true)
        
        if(self.mainScreenUv != nil){
            if(self.mainScreenUv.destPointMarker != nil){
                self.mainScreenUv.destPointMarker.map = nil
                self.mainScreenUv.destPointMarker = nil
            }
        }

        if(isPickUpMode == true){
            
            // Open PickUp selection
            if(self.mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == false && isOpenSelection == true){
                if(mainScreenUv.requestPickUpView != nil){
                    tmpPickUpPointView.frame = CGRect(x: self.mainScreenUv.gMapView.projection.point(for: self.mainScreenUv.pickUpLocation.coordinate).x, y: self.mainScreenUv.gMapView.projection.point(for: self.mainScreenUv.pickUpLocation.coordinate).y, width: 20, height: 20)
                    
                    self.mainScreenUv!.currentTransition = JTMaterialTransition(animatedView: tmpPickUpPointView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35)!)
                    launchPlaceFinder(centerLocation: self.mainScreenUv.pickUpLocation)
                }else{
                    self.mainScreenUv!.currentTransition = JTMaterialTransition(animatedView: self.pickUpPointView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35)!)
                    launchPlaceFinder(centerLocation: self.mainScreenUv.pickUpLocation)
                }
            }
            
        }else{
            
            isPickUpMode = true
            if(handler != nil){
                handler(true,view)
            }
            
            // View PickUp View
            pickUpViewLeftMargin.constant = 0
            pickUpViewRightMargin.constant = 0
            
            destViewLeftMargin.constant = 10
            destViewRightMargin.constant = 10
            
            self.pickUpView.backgroundColor = UIColor(hex: 0xFFFFFF)
            self.destView.backgroundColor = UIColor(hex: 0xF3F3F3)
            
            UIView.transition(
                with: self,
                duration: 0.25,
                options: UIViewAnimationOptions.showHideTransitionViews,
                animations: { () -> Void in
//                    self.pickUpView.zPosition = 1
//                    self.destView.zPosition = -1
                    
                    self.view.bringSubview(toFront: self.pickUpView)
                    
                    self.destHPointView.isHidden = true
                    self.destLocHLbl.isHidden = true
                    self.addDestImgView.isHidden = true
                    
                    self.destAddressLbl.isHidden = false
                    self.destAddPointView.isHidden = false
                    
                    self.pickUpAddressLbl.isHidden = false
                    if(self.isDriverAssigned == false){
                        self.addPickUpImgView.isHidden = false
                    }
                    
                    self.pickUpAddressLbl.text = self.pickUpLocHLbl.text!
                    self.pickUpLocHLbl.text = self.generalFunc.getLanguageLabel(origValue: "Pickup from", key: "LBL_PICK_UP_FROM")
                    
                    self.setLocationIndicator()
                    
                    self.view.layoutIfNeeded()
            },
                completion: nil
            )
            
            if(mainScreenUv != nil && mainScreenUv.sourcePinImgView.isHidden == false){
                mainScreenUv.sourcePinImgView.image = UIImage(named: "ic_pin_source")
                mainScreenUv.sourcePickUpEtaLbl.isHidden = false
            }
            
            goToPickLoc()
            
        }
    
    }
    
    func destViewTappedOnAction(){
        destViewTapped(isAutoOpenSelection: true)
    }
    
    func continueDestSelection(){
        var centerLoc = self.mainScreenUv.destLocation != nil ? self.mainScreenUv.destLocation! : self.mainScreenUv.pickUpLocation!
        
        if(self.mainScreenUv.isDriverAssigned == true && (self.mainScreenUv.destLocation == nil || self.mainScreenUv.destAddress == "")){
            centerLoc = self.mainScreenUv.pickUpLocation
        }
        
        if(mainScreenUv.requestPickUpView != nil){
            tmpDestPointView.frame = CGRect(x: self.mainScreenUv.gMapView.projection.point(for: centerLoc.coordinate).x, y: self.mainScreenUv.gMapView.projection.point(for: centerLoc.coordinate).y, width: 20, height: 20)
            self.mainScreenUv!.currentTransition = JTMaterialTransition(animatedView: self.tmpDestPointView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35)!)
            
        }else{
            self.mainScreenUv!.currentTransition = JTMaterialTransition(animatedView: self.destHPointView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35)!)
        }
        
        launchPlaceFinder(centerLocation: centerLoc)
    }
    
    func destViewTapped(isAutoOpenSelection:Bool){
        
        if(mainScreenUv != nil){
            if(mainScreenUv.userProfileJson.get("APP_DESTINATION_MODE").uppercased() == "NONE"){
                return
            }
            
            if(self.mainScreenUv.pickUpLocation == nil){
                return
            }
        }
        
        mainScreenUv.sourcePinImgViewWidth.constant = 50
        mainScreenUv.sourcePinImgViewHeight.constant = 50
        mainScreenUv.sourcePinImgViewOffset.constant = -25
        
        if(self.mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == true  && self.mainScreenUv.destLocation != nil && self.mainScreenUv.destAddress != ""){
            if(self.mainScreenUv.destPointMarker != nil){
                self.mainScreenUv.destPointMarker.map = nil
            }
           
            if(self.mainScreenUv.isTripStarted == false){
                self.mainScreenUv.destPointMarker = GMSMarker()
                self.mainScreenUv.destPointMarker.icon = UIImage(named: "ic_destination_place_image")
                self.mainScreenUv.destPointMarker.position = self.mainScreenUv.destLocation.coordinate
                self.mainScreenUv.destPointMarker.map = self.mainScreenUv.gMapView
            }
            
            if(isPickUpMode == false){
                goToDestLoc()
            }
        }
        
        if(self.mainScreenUv != nil){
            if(self.mainScreenUv.pickUpPointMarker != nil){
                self.mainScreenUv.pickUpPointMarker.map = nil
                self.mainScreenUv.pickUpPointMarker = nil
            }
        }
        
        if(isPickUpMode == false){
            //Open Dest Selection
            if(self.mainScreenUv != nil && (self.mainScreenUv.isDriverAssigned == false || (self.mainScreenUv.isDriverAssigned == true && (self.mainScreenUv.destLocation == nil || self.mainScreenUv.destAddress == "") ))){
               self.continueDestSelection()
            }else{
                if(self.mainScreenUv.isDriverAssigned == true && self.mainScreenUv.userProfileJson.get("IS_DEST_ANYTIME_CHANGE").uppercased() == "YES" && self.mainScreenUv.eTripType.uppercased() == Utils.cabGeneralType_Ride.uppercased() ){
                    self.continueDestSelection()
                }
            }
            
        }else{
            if(handler != nil){
                handler(false,view)
            }
            isPickUpMode = false
            // visible dest view
            
            pickUpViewLeftMargin.constant = 10
            pickUpViewRightMargin.constant = 10
            
            destViewLeftMargin.constant = 0
            destViewRightMargin.constant = 0
            
            self.pickUpView.backgroundColor = UIColor(hex: 0xF3F3F3)
            self.destView.backgroundColor = UIColor(hex: 0xFFFFFF)
            
//            UIViewAnimationOptions
            
            UIView.transition(
                with: self,
                duration: 0.25,
                options: UIViewAnimationOptions.showHideTransitionViews,
                animations: { () -> Void in
//                    self.pickUpView.zPosition = -1
//                    self.destView.zPosition = 1

                    self.view.bringSubview(toFront: self.destView)
                    
                    self.destHPointView.isHidden = false
                    self.destLocHLbl.isHidden = false
                    
//                    if(self.isDriverAssigned == false){
//                        self.addDestImgView.isHidden = false
//                    }
                    
                    if(self.mainScreenUv != nil && self.mainScreenUv.isDriverAssigned == true && self.mainScreenUv.userProfileJson.get("IS_DEST_ANYTIME_CHANGE").uppercased() == "YES" && self.mainScreenUv.eTripType.uppercased() == Utils.cabGeneralType_Ride.uppercased() ){
                        self.addDestImgView.isHidden = false
                        if(self.mainScreenUv.destAddress != ""){
                            self.addDestImgView.image = UIImage(named: "ic_edit")!
                        }
                        
                        if(self.isPickUpMode == true){
                            self.addDestImgYPosition.constant = 10
                        }else{
                            self.addDestImgYPosition.constant = 0
                        }
                        
                    }else if(self.isDriverAssigned == false){
                        self.addDestImgView.isHidden = false
                    }
                    
                    self.destAddressLbl.isHidden = false
                    self.destAddPointView.isHidden = true
                    
                    self.pickUpAddressLbl.isHidden = true
                    self.addPickUpImgView.isHidden = true
                    
                    
                    self.pickUpLocHLbl.text = self.pickUpAddressLbl.text!
                    
                    self.setLocationIndicator()
                    
                    self.view.layoutIfNeeded()
                    
                    
            },
                completion: nil
            )
            
            if(mainScreenUv != nil && mainScreenUv.sourcePinImgView.isHidden == false){
                mainScreenUv.sourcePinImgView.image = UIImage(named: "ic_pin_dest_selection")
                
                mainScreenUv.sourcePickUpEtaLbl.isHidden = true
            }
            
            
            if(self.mainScreenUv != nil){
                if((self.mainScreenUv.destLocation == nil || self.mainScreenUv.destAddress == "") && isAutoOpenSelection == true){
                    let centerLoc = self.mainScreenUv.destLocation != nil ? self.mainScreenUv.destLocation! : self.mainScreenUv.pickUpLocation!
                    
                    self.mainScreenUv!.currentTransition = JTMaterialTransition(animatedView: self.destHPointView, bgColor: UIColor.UCAColor.AppThemeColor.lighter(by: 35)!)
                    
                    launchPlaceFinder(centerLocation: centerLoc)
                }else{
                    goToDestLoc()
                }
            }else{
                goToDestLoc()
            }
            
        }
    }
    
    func goToPickLoc(){
        if(self.mainScreenUv == nil || self.mainScreenUv.gMapView == nil || self.mainScreenUv.pickUpLocation == nil){
            return
        }
        
        let maxZoomLevel = self.mainScreenUv.gMapView.maxZoom
        
        var bounds = GMSCoordinateBounds()
        bounds = bounds.includingCoordinate(self.mainScreenUv.pickUpLocation.coordinate)
        self.mainScreenUv.gMapView.setMinZoom(self.mainScreenUv.gMapView.minZoom, maxZoom: self.mainScreenUv.gMapView.maxZoom - 5)

        CATransaction.begin()
        CATransaction.setCompletionBlock {
              self.mainScreenUv.gMapView.setMinZoom(self.mainScreenUv.gMapView.minZoom, maxZoom: maxZoomLevel)
        }
        self.mainScreenUv.gMapView.animate(with: GMSCameraUpdate.fit(bounds, with: UIEdgeInsetsMake(self.mainScreenUv.isDriverAssigned == false ? 0 : (self.mainScreenUv.addressBarYPosition + self.mainScreenUv.addressBarHeight + (self.mainScreenUv.isDriverAssigned == true && self.mainScreenUv.isTripStarted == false ? self.mainScreenUv.heightOfWaitingMarker : 0)), 0, 0, 0)))
        CATransaction.commit()
    }
    
    func goToDestLoc(){
        if(self.mainScreenUv == nil || self.mainScreenUv.gMapView == nil || self.mainScreenUv.destLocation == nil){
            return
        }
//        let camera = GMSCameraPosition.camera(withLatitude: self.mainScreenUv.destLocation.coordinate.latitude,
//                                              longitude: self.mainScreenUv.destLocation.coordinate.longitude, zoom: self.mainScreenUv.gMapView.camera.zoom)
//
//        self.mainScreenUv.gMapView.animate(to: camera)
        
        let maxZoomLevel = self.mainScreenUv.gMapView.maxZoom
        
        var bounds = GMSCoordinateBounds()
        bounds = bounds.includingCoordinate(self.mainScreenUv.destLocation.coordinate)
        self.mainScreenUv.gMapView.setMinZoom(self.mainScreenUv.gMapView.minZoom, maxZoom: self.mainScreenUv.gMapView.maxZoom - 5)
        
        CATransaction.begin()
        CATransaction.setCompletionBlock {
            if(self.mainScreenUv != nil && self.mainScreenUv.gMapView != nil){
                self.mainScreenUv.gMapView.setMinZoom(self.mainScreenUv.gMapView.minZoom, maxZoom: maxZoomLevel)
            }
        }
        self.mainScreenUv.gMapView.animate(with: GMSCameraUpdate.fit(bounds, with: UIEdgeInsetsMake(self.mainScreenUv.addressBarYPosition + self.mainScreenUv.addressBarHeight + (self.mainScreenUv.isTripStarted ? self.mainScreenUv.heightOfWaitingMarker : 0), 0, 0, 0)))
        CATransaction.commit()
        
    }
    func loadViewFromNib() -> UIView {
        
        let bundle = Bundle(for: type(of: self))
        let nib = UINib(nibName: "AddressContainerView", bundle: bundle)
        let view = nib.instantiate(withOwner: self, options: nil)[0] as! UIView
        
        return view
    }
    
    func launchPlaceFinder(centerLocation:CLLocation?){
        let launchPlaceFinder = LaunchPlaceFinder(viewControllerUV: self.mainScreenUv)
        launchPlaceFinder.currInst = launchPlaceFinder
        launchPlaceFinder.SCREEN_TYPE = mainScreenUv.isPickUpMode == true ? "PICKUP" : "DESTINATION"
        if(mainScreenUv != nil){
            launchPlaceFinder.isDriverAssigned = self.mainScreenUv.isDriverAssigned
            launchPlaceFinder.currentCabType = self.mainScreenUv.currentCabGeneralType
        }
        if(centerLocation != nil){
            launchPlaceFinder.setBiasLocation(sourceLocationPlaceLatitude: centerLocation!.coordinate.latitude, sourceLocationPlaceLongitude: centerLocation!.coordinate.longitude)
        }
        
        launchPlaceFinder.initializeFinder { (address, latitude, longitude) in
            
            if(self.mainScreenUv != nil){
                self.mainScreenUv.setTripLocation(selectedAddress: address, selectedLocation: CLLocation(latitude: latitude, longitude: longitude))
            }
        }
    }

}
