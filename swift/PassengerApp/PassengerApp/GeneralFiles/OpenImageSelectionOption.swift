//
//  OpenImageSelectionOption.swift
//  PassengerApp
//
//  Created by NEW MAC on 16/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import AVFoundation


class OpenImageSelectionOption: NSObject, UIImagePickerControllerDelegate, UINavigationControllerDelegate {
    
    typealias ImageUploadCompletionHandler = (_ isImageUpload:Bool) -> Void
    
    var uv:UIViewController!

    let generalFunc = GeneralFunctions()
    
    var overlayView:UIView!
    var selectionAreaView:UIView!
    
    var loadingDialog:NBMaterialLoadingDialog!
    
    var imageUploadCompletionHandler:ImageUploadCompletionHandler!
    
    init(uv: UIViewController) {
        self.uv = uv
        super.init()
    }
    
    func show(imageUploadCompletionHandler:@escaping ImageUploadCompletionHandler){
        
        self.imageUploadCompletionHandler = imageUploadCompletionHandler
        
        let chooseImageOptionView = self.generalFunc.loadView(nibName: "ChooseImageOptionView")
        
        chooseImageOptionView.frame = CGRect(x:0, y: self.uv.view.frame.height - 80, width: Application.screenSize.width, height: 80)

        let overlayView = UIView()
        overlayView.frame = self.uv.view.frame
        
        
        overlayView.backgroundColor = UIColor.black
        overlayView.alpha = 0.4
        self.overlayView = overlayView
        
        self.uv.view.addSubview(overlayView)
        
        chooseImageOptionView.layer.shadowOpacity = 0.5
        chooseImageOptionView.layer.shadowOffset = CGSize(width: 0, height: 3)
        chooseImageOptionView.layer.shadowColor = UIColor.black.cgColor
        
        self.selectionAreaView = chooseImageOptionView
        
        self.uv.view.addSubview(chooseImageOptionView)
        
        (chooseImageOptionView.subviews[0] as! MyLabel).text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CHOOSE_CATEGORY")
        
        let closeTapGue = UITapGestureRecognizer()
        closeTapGue.addTarget(self, action: #selector(self.closeSlectionView))
        
        chooseImageOptionView.subviews[3].isUserInteractionEnabled = true
        chooseImageOptionView.subviews[3].addGestureRecognizer(closeTapGue)
        
        let cameraTapGue = UITapGestureRecognizer()
        cameraTapGue.addTarget(self, action: #selector(self.cameraTapped))
        
        chooseImageOptionView.subviews[1].isUserInteractionEnabled = true
        chooseImageOptionView.subviews[1].addGestureRecognizer(cameraTapGue)
        
        let gallaryTapGue = UITapGestureRecognizer()
        gallaryTapGue.addTarget(self, action: #selector(self.gallaryTapped))
        
        chooseImageOptionView.subviews[2].isUserInteractionEnabled = true
        chooseImageOptionView.subviews[2].addGestureRecognizer(gallaryTapGue)
    }
    
    func closeSlectionView(){
        self.overlayView!.removeFromSuperview()
        self.selectionAreaView!.removeFromSuperview()
    }
    
    func cameraTapped(){
        let imagePickerController = UIImagePickerController()
        
        imagePickerController.sourceType = .camera
        
        imagePickerController.delegate = self
        imagePickerController.allowsEditing = true
        self.uv.present(imagePickerController, animated: true, completion: nil)
    }
    
    func gallaryTapped(){
        let imagePickerController = UIImagePickerController()
        
        imagePickerController.sourceType = .photoLibrary
        
        imagePickerController.delegate = self
        imagePickerController.allowsEditing = true
        Configurations.setAppThemeNavBar()
        
        self.uv.present(imagePickerController, animated: true, completion: nil)
    }
    
    // MARK: UIImagePickerControllerDelegate
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        // Dismiss the picker if the user canceled.
        picker.dismiss(animated: true, completion: nil)
    }
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : Any]) {
        // The info dictionary contains multiple representations of the image, and this uses the original.
        let selectedImage = info[UIImagePickerControllerEditedImage] as! UIImage
        
        if(selectedImage.size.width < Utils.ImageUpload_MINIMUM_WIDTH || selectedImage.size.height < Utils.ImageUpload_MINIMUM_HEIGHT){
            self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MIN_RES_IMAGE"))
            return
        }
        
        picker.dismiss(animated: true, completion: {
            DispatchQueue.main.async() {
                self.requestUploadImage(image: selectedImage.correctlyOrientedImage().cropTo(size: CGSize(width: Utils.ImageUpload_DESIREDWIDTH, height: Utils.ImageUpload_DESIREDHEIGHT)))
            }
        })
        
    }
    
    func requestUploadImage(image:UIImage){
    
        let userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        let SITE_TYPE_DEMO_MSG = userProfileJson.get("SITE_TYPE_DEMO_MSG")
        
//        if let SITE_TYPE = GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) as? String{
//            if(SITE_TYPE == "Demo"){
//                self.generalFunc.setError(uv: self.uv, title: "", content: SITE_TYPE_DEMO_MSG)
//                return
//            }
//        }
        
        if let SITE_TYPE = GeneralFunctions.getValue(key: Utils.SITE_TYPE_KEY) as? String{
            if(SITE_TYPE == "Demo" && userProfileJson.get("vEmail") == "rider@gmail.com"){
                self.generalFunc.setError(uv: self.uv, title: "", content: SITE_TYPE_DEMO_MSG)
                return
            }
        }
        
        myImageUploadRequest(image: image)
    }
    
    func myImageUploadRequest(image:UIImage){
        let myUrl = URL(string: CommonUtils.webservice_path)
        
        let request = NSMutableURLRequest(url:myUrl!);
        request.httpMethod = "POST"
        
        let parameters = [
            "type"  : "uploadImage",
            "MemberType"    : Utils.appUserType,
            "iMemberId"    : GeneralFunctions.getMemberd()
        ]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.uv.view, isOpenLoader: true)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.uploadImage(image:image, completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    GeneralFunctions.saveValue(key: Utils.USER_PROFILE_DICT_KEY, value: response as AnyObject)
                    
                    self.closeSlectionView()
                    
                    if(self.imageUploadCompletionHandler != nil){
                        self.imageUploadCompletionHandler(true)
                    }
                }else{
                    self.generalFunc.setError(uv: self.uv, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                }
                
            }else{
                self.generalFunc.setError(uv: self.uv)
            }
        })
        
        
    }
}
