//
//  RatingUV.swift
//  DriverApp
//
//  Created by NEW MAC on 30/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import GoogleMaps

class RatingUV: UIViewController, OnLocationUpdateDelegate, MyBtnClickDelegate, UITextViewDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var googleMapContainerView: UIView!
    @IBOutlet weak var ratingBar: RatingView!
    @IBOutlet weak var rateHLbl: MyLabel!
    @IBOutlet weak var commentTextView: KMPlaceholderTextView!
    @IBOutlet weak var submitBtn: MyButton!
    @IBOutlet weak var nameLbl: MyLabel!
    
    var tripData:NSDictionary!
    
    var window:UIWindow!
    
    let generalFunc = GeneralFunctions()
    
    var isPageLoaded = false
    
    var getLocation:GetLocation!
    
    var isFirstLocationUpdate = true
    
    var gMapView:GMSMapView!
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    deinit {
        releaseAllTask()
    }
    override func viewDidLoad() {
        super.viewDidLoad()

        window = Application.window!
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "RatingScreenDesign", uv: self, contentView: contentView))

        commentTextView.delegate = self
    }

    override func viewDidAppear(_ animated: Bool) {
        if(isPageLoaded == false){
            
            isPageLoaded = true
            
            let camera = GMSCameraPosition.camera(withLatitude: 0.0, longitude: 0.0, zoom: Utils.defaultZoomLevel)
            gMapView = GMSMapView.map(withFrame: self.googleMapContainerView.frame, camera: camera)
            //        googleMapContainerView = gMapView
            //        gMapView = GMSMapView()
            gMapView.isMyLocationEnabled = true
            self.googleMapContainerView.addSubview(gMapView)
            
            setData()
        }
        UIApplication.shared.isStatusBarHidden = false
    }
    
//    override func viewDidLayoutSubviews() {
//        UIApplication.shared.isStatusBarHidden = false
//    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RATING")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RATING")
        
        self.rateHLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RATE")
        self.commentTextView.placeholder = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_WRITE_COMMENT_HINT_TXT")
        self.submitBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_SUBMIT_TXT"))
        
        self.getLocation = GetLocation(uv: self, isContinuous: true)
        self.getLocation.buildLocManager(locationUpdateDelegate: self)
        
        NotificationCenter.default.addObserver(self, selector: #selector(self.releaseAllTask), name: NSNotification.Name(rawValue: Utils.releaseAllTaskObserverKey), object: nil)

        self.submitBtn.clickDelegate = self
        
        self.nameLbl.text = self.tripData!.get("PName")
    }
    
    func onLocationUpdate(location: CLLocation) {
        var currentZoomLevel:Float = self.gMapView.camera.zoom
        
        if(currentZoomLevel < Utils.defaultZoomLevel && isFirstLocationUpdate == true){
            currentZoomLevel = Utils.defaultZoomLevel
        }
        let camera = GMSCameraPosition.camera(withLatitude: location.coordinate.latitude,
                                              longitude: location.coordinate.longitude, zoom: currentZoomLevel)
        
        self.gMapView.animate(to: camera)
        
        isFirstLocationUpdate = false
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
        
        GeneralFunctions.removeObserver(obj: self)
        
        
        if(isDismiss){
            self.dismiss(animated: false, completion: nil)
            self.navigationController?.dismiss(animated: false, completion: nil)
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.submitBtn){
            
            if(self.ratingBar.rating > 0.0){
                submitRating()
            }else{
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ERROR_RATING_DIALOG_TXT"), uv: self)
            }
        }
    }
    
    func submitRating(){
        
        let parameters = ["type":"submitRating","iGeneralUserId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType, "tripID": tripData!.get("TripId"), "rating": "\(self.ratingBar.rating)", "message": "\(commentTextView.text!)"]
        
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
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
        })
    }
    
    func loadTripFinishView(){
        let tripFinishView = self.generalFunc.loadView(nibName: "TripFinishView", uv: self, isWithOutSize: true)
        
        let width = Application.screenSize.width  > 380 ? 370 : Application.screenSize.width - 50
        
        tripFinishView.frame.size = CGSize(width: width, height: 300)
        
        
        tripFinishView.center = CGPoint(x: self.contentView.bounds.midX, y: self.contentView.bounds.midY)
        
        let bgView = UIView()
        bgView.frame = self.contentView.frame
        
        bgView.backgroundColor = UIColor.black
        
        bgView.isUserInteractionEnabled = true
        
        tripFinishView.layer.shadowOpacity = 0.5
        tripFinishView.layer.shadowOffset = CGSize(width: 0, height: 3)
        tripFinishView.layer.shadowColor = UIColor.black.cgColor
        
        self.view.addSubview(bgView)
        self.view.addSubview(tripFinishView)
        
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
        
        (tripFinishView.subviews[3].subviews[0] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "OK THANKS", key: "LBL_OK_THANKS").uppercased()
        
        
        let okTapGue = UITapGestureRecognizer()
        
        okTapGue.addTarget(self, action: #selector(self.tripFinishOkTapped))
        
        (tripFinishView.subviews[3].subviews[0] as! MyLabel).isUserInteractionEnabled = true
        
        (tripFinishView.subviews[3].subviews[0] as! MyLabel).addGestureRecognizer(okTapGue)
    }
    
    func tripFinishOkTapped(){
        
        releaseAllTask()
        
        let getUserData = GetUserData(uv: self, window: self.window!)
        getUserData.getdata()
    }

    //MARK:- TextView Delegate Method
    func textViewDidEndEditing(_ textView: UITextView) {
        UIApplication.shared.isStatusBarHidden = true
        UIApplication.shared.isStatusBarHidden = false
    }
}
