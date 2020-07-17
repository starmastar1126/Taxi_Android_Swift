//
//  FareBreakDownUV.swift
//  DriverApp
//
//  Created by NEW MAC on 26/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import CoreLocation

class FareBreakDownUV: UIViewController {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var headerView: UIView!
    @IBOutlet weak var noteLbl: MyLabel!
    @IBOutlet weak var vehicleTypeLbl: MyLabel!
    @IBOutlet weak var fareContainerStackView: UIStackView!
    @IBOutlet weak var fareContainerStackViewHeight: NSLayoutConstraint!
    @IBOutlet weak var detailsContainerView: UIView!
    @IBOutlet weak var detailsContainerViewHeight: NSLayoutConstraint!
    

    var selectedCabTypeName = ""
    let generalFunc = GeneralFunctions()
    
    var selectedCabTypeId = ""
    
    var promoCode = ""
    
    var loaderView:UIView!
    
    var pickUpLocation:CLLocation!
    var destLocation:CLLocation!
    
    var time = ""
    var distance = ""
    
    var isPageLoad = false
    
    var isFirstLaunch = true
    var cntView:UIView!
    
    var isDestinationAdded = "Yes"
    
    
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        self.navigationController?.navigationBar.layer.zPosition = 1
    }
    
    override func viewDidAppear(_ animated: Bool) {
        self.navigationController?.navigationBar.layer.zPosition = -1
        
        if(isFirstLaunch){
            
            cntView.frame.size = CGSize(width: cntView.frame.width, height: 600)
            self.scrollView.bounces = false
            //            self.scrollView.setContentViewSize(offset: 15, currentMaxHeight: self.scrollViewCOntentViewHeight.constant)
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: 600)
            self.scrollView.backgroundColor = UIColor(hex: 0xf2f2f4)
            cntView.backgroundColor = UIColor(hex: 0xf2f2f4)
            
            isFirstLaunch = false
            
            
            self.getData()
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        cntView = self.generalFunc.loadView(nibName: "FareBreakDownScreenDesign", uv: self, contentView: scrollView)
        
        self.scrollView.addSubview(cntView)
        
        self.addBackBarBtn()
        
        self.headerView.backgroundColor = UIColor.UCAColor.AppThemeColor
        
        vehicleTypeLbl.text = selectedCabTypeName
        addLoader()
        setData()
        
        self.detailsContainerView.isHidden = true
        
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FARE_BREAKDOWN_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FARE_BREAKDOWN_TXT")
        
        self.noteLbl.text = self.generalFunc.getLanguageLabel(origValue: "This fare is based on our estimation. This may vary during trip and final fare.", key: "LBL_GENERAL_NOTE_FARE_EST")
        
       
        
        
        self.noteLbl.fitText()
        
        self.detailsContainerView.layer.shadowOpacity = 0.5
        self.detailsContainerView.layer.shadowOffset = CGSize(width: 0, height: 3)
        self.detailsContainerView.layer.shadowColor = UIColor.black.cgColor
        self.detailsContainerView.layer.cornerRadius = 10
        self.detailsContainerView.layer.masksToBounds = true
    }
    
    func addLoader(){
        if(loaderView != nil){
            loaderView.removeFromSuperview()
        }
        
        loaderView =  self.generalFunc.addMDloader(contentView: self.view)
        loaderView.backgroundColor = UIColor.clear
    }
    func getData(){
        
        if(self.destLocation == nil){
            self.isDestinationAdded = "No"
        }
        
        if(self.destLocation != nil && self.destLocation.coordinate.latitude == 0.0 && self.destLocation.coordinate.longitude == 0.0){
            self.destLocation = self.pickUpLocation
        }
        
        let destLoc = self.destLocation != nil ? self.destLocation : self.pickUpLocation
        
        let directionURL = "https://maps.googleapis.com/maps/api/directions/json?origin=\(self.pickUpLocation!.coordinate.latitude),\(self.pickUpLocation!.coordinate.longitude)&destination=\(destLoc!.coordinate.latitude),\(destLoc!.coordinate.longitude)&key=\(Configurations.getInfoPlistValue(key: "GOOGLE_SERVER_KEY"))&language=\(Configurations.getGoogleMapLngCode())&sensor=true"
        
        let exeWebServerUrl = ExeServerUrl(dict_data: [String:String](), currentView: self.view, isOpenLoader: false)
        
        exeWebServerUrl.executeGetProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("status").uppercased() != "OK" || dataDict.getArrObj("routes").count == 0){
                    self.isDestinationAdded = "No"
                    self.continueEstimateFare(distance: "", time: "")
                    return
                }
                
                if(self.destLocation == nil){
                    self.isDestinationAdded = "No"
                }
                
                
                let routesArr = dataDict.getArrObj("routes")
                let legs_arr = (routesArr.object(at: 0) as! NSDictionary).getArrObj("legs")
                let duration = (legs_arr.object(at: 0) as! NSDictionary).getObj("duration").get("value")
                let distance = (legs_arr.object(at: 0) as! NSDictionary).getObj("distance").get("value")
                
                self.continueEstimateFare(distance: distance, time: duration)
                
            }else{
                //                self.generalFunc.setError(uv: self)
            }
        }, url: directionURL)
    }
    
    func continueEstimateFare(distance:String, time:String){
        var parameters = ["type":"getEstimateFareDetailsArr","SelectedCar": self.selectedCabTypeId, "distance": distance, "time": time, "iUserId": GeneralFunctions.getMemberd(), "PromoCode": promoCode, "isDestinationAdded": self.isDestinationAdded, "iMemberId": GeneralFunctions.getMemberd(), "UserType": Utils.appUserType]
        
        if(pickUpLocation != nil){
            parameters["StartLatitude"] = "\(self.pickUpLocation!.coordinate.latitude)"
            parameters["EndLongitude"] = "\(self.pickUpLocation!.coordinate.longitude)"
        }
        
        if(destLocation != nil){
            parameters["DestLatitude"] = "\(self.destLocation!.coordinate.latitude)"
            parameters["DestLongitude"] = "\(self.destLocation!.coordinate.longitude)"
        }
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            //            print("Response:\(response)")
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.addFareDetails(msgArr: dataDict.getArrObj(Utils.message_str))
                }else{
                    self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            self.loaderView.isHidden = true
        })
    }
    
    func addFareDetails(msgArr:NSArray){
        
        var currentYposition:CGFloat = 0
        var currentPosition = 0
        
        for i in 0..<msgArr.count {
            
            let dict_temp = msgArr[i] as! NSDictionary
            
            for (key, value) in dict_temp {
                //                print("\(key): \(value)")
                
                let viewCus = self.generalFunc.loadView(nibName: "FareDataItemView", uv: self, isWithOutSize: true)
                let frame = CGRect(x: 0, y: currentYposition, width: self.fareContainerStackView.frame.width, height: 40)
                viewCus.frame = frame
                
                let stView = viewCus.subviews[0] as! UIStackView
                
                let lblTitle = stView.subviews[0] as! MyLabel
                let lblValue = stView.subviews[1] as! MyLabel
                
                lblTitle.text = Configurations.convertNumToAppLocal(numStr: key as! String)
                lblValue.text = Configurations.convertNumToAppLocal(numStr: value as! String)
                
                self.fareContainerStackView.addArrangedSubview(viewCus)
                
                self.fareContainerStackViewHeight.constant = self.fareContainerStackViewHeight.constant + 40
                
                currentYposition = currentYposition + 40
                currentPosition = currentPosition + 1
                
                if(Configurations.isRTLMode()){
                    lblValue.textAlignment = .left
                }else{
                    lblValue.textAlignment = .right
                }
            }
        }
        
        if(isDestinationAdded == "Yes"){
            self.fareContainerStackView.subviews[msgArr.count - 1].backgroundColor = UIColor(hex: 0xe3e3e3)
            (self.fareContainerStackView.subviews[msgArr.count - 1].subviews[0].subviews[0] as! MyLabel).textColor = UIColor(hex: 0x000000)
            (self.fareContainerStackView.subviews[msgArr.count - 1].subviews[0].subviews[1] as! MyLabel).textColor = UIColor.UCAColor.AppThemeColor
        }
        
        self.fareContainerStackViewHeight.constant = self.fareContainerStackViewHeight.constant - 45
        
        //        chargesContainerView.frame.size = CGSize(width: chargesContainerView.frame.width, height: CGFloat((55 * HistoryFareDetailsNewArr.count)))
        
        //        self.chargesParentView.frame.size = CGSize(width: chargesParentView.frame.width, height: chargesParentView.frame.height + chargesContainerView.frame.height - 50)
        self.detailsContainerViewHeight.constant = self.fareContainerStackViewHeight.constant + 55
        
        
        self.fareContainerStackView.layoutIfNeeded()
        
        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
            self.cntView.frame.size = CGSize(width: self.cntView.frame.width, height: self.detailsContainerView.frame.maxY + 20)
            
            //            self.scrollView.setContentViewSize(offset: 15, currentMaxHeight: self.scrollViewCOntentViewHeight.constant)
            self.scrollView.contentSize = CGSize(width: self.scrollView.contentSize.width, height: self.detailsContainerView.frame.maxY + 20)
        })
        
        self.detailsContainerView.isHidden = false
        
        
    }
}
