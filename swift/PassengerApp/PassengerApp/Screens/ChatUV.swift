//
//  ChatUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 01/08/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import AVFoundation
import FirebaseStorage

class ChatUV: JSQMessagesViewController {
    
    
    let generalFunc = GeneralFunctions()
    var receiverId = ""
    var assignedtripId = ""
    var messageId = ""
    var receiverDisplayName = ""
    var pPicName = ""
    
    var messages = [JSQMessage]()
    
    var senderImg:JSQMessagesAvatarImage!
    var receiverImg:JSQMessagesAvatarImage!
    
    var senderImgView = UIImageView()
    var receiverImgView = UIImageView()
    
    
    var userProfileJson : NSDictionary!
    
    // BUBBLE PROPERTY
    lazy var outgoingBubble: JSQMessagesBubbleImage = {
        return JSQMessagesBubbleImageFactory()!.outgoingMessagesBubbleImage(with: UIColor(hex: 0x1087FF))
    }()
    
    lazy var incomingBubble: JSQMessagesBubbleImage = {
        return JSQMessagesBubbleImageFactory()!.incomingMessagesBubbleImage(with: UIColor(hex: 0xB6B6B6))
    }()
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
        self.addBackBarBtn()
        
        inputToolbar.preferredDefaultHeight = 50
        inputToolbar.maximumHeight = 100
        
        GeneralFunctions.saveValue(key: "cahtViewVisible", value:true as Bool as AnyObject)
        
        userProfileJson = (GeneralFunctions.getValue(key: Utils.USER_PROFILE_DICT_KEY) as! String).getJsonDataDict().getObj(Utils.message_str)
        
        let defaultImgView = UIImageView(frame: CGRect(x: 0, y:0, width: 60, height: 60))
        defaultImgView.image = UIImage(named: "ic_no_pic_user")
        
        self.senderImg = JSQMessagesAvatarImageFactory.avatarImage(withPlaceholder: defaultImgView.image, diameter: 60)!
        self.receiverImg = JSQMessagesAvatarImageFactory.avatarImage(withPlaceholder: defaultImgView.image, diameter: 60)!
//        + "_" + assignedtripId + "_\(Utils.appUserType)"
        senderId = Utils.appUserType
        senderDisplayName = Utils.appUserType
        
        messageId = receiverId + "_" + assignedtripId + "_Driver"
        
        title = "\(receiverDisplayName)"
        
        inputToolbar.contentView.leftBarButtonItem = nil
        
        inputToolbar.contentView.textView.placeHolder = self.generalFunc.getLanguageLabel(origValue: "Enter new message", key: "LBL_ENTER_MESSAGE")
        
        inputToolbar.contentView.textView.accessibilityLabel = self.generalFunc.getLanguageLabel(origValue: "Enter new message", key: "LBL_ENTER_MESSAGE")
        
        senderImgView.frame.size = CGSize(width: 60, height:60)
        receiverImgView.frame.size = CGSize(width: 60, height:60)
        
        let rightButton = UIButton(type: .custom)
        rightButton.setImage(UIImage(named: "ic_send_msg_arrow")!.withRenderingMode(.alwaysTemplate), for: .normal)
        rightButton.tintColor = UIColor.UCAColor.AppThemeColor
        inputToolbar.contentView.rightBarButtonItem = rightButton
        inputToolbar.contentView.rightBarButtonItemWidth = 50
        
        inputToolbar.contentView.textView.backgroundColor = UIColor.clear
        inputToolbar.contentView.textView.borderWidth = 0
        
        
        collectionView.collectionViewLayout.incomingAvatarViewSize = CGSize(width: 60, height:60)
        collectionView.collectionViewLayout.outgoingAvatarViewSize = CGSize(width: 60, height:60)
        
        
        let query = Constants.refs.databaseChats.child(assignedtripId + "-Trip").queryOrderedByPriority()
        
        query.observe(.childAdded, with: { (snapshot) -> Void in
            if(snapshot != nil && snapshot.value != nil){
                let data = snapshot.value as! [String: String]
                
                let dataDict = data as! NSDictionary
                
                let senderID = dataDict.get("eUserType") == Utils.appUserType ? dataDict.get("passengerId") : dataDict.get("driverId")
                
                if let message = JSQMessage(senderId: dataDict.get("eUserType"), displayName: dataDict.get("eUserType"), text: dataDict.get("Text"))
                {
                    self.messages.append(message)
                    
                    self.finishReceivingMessage()
                    
                }
            }
        })
        
        if Configurations.isRTLMode(){
            //            collectionView.semanticContentAttribute = .forceLeftToRight
            outgoingBubble = JSQMessagesBubbleImageFactory()!.incomingMessagesBubbleImage(with: UIColor(hex: 0x1087FF))
            incomingBubble = JSQMessagesBubbleImageFactory()!.outgoingMessagesBubbleImage(with: UIColor(hex: 0xB6B6B6))
            inputToolbar.contentView.textView.textAlignment = .right
        }
        
        
        senderImgView.sd_setImage(with: URL(string: CommonUtils.user_image_url + GeneralFunctions.getMemberd() + "/" + userProfileJson.get("vImgName")), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            
            self.senderImg = JSQMessagesAvatarImageFactory.avatarImage(withPlaceholder: self.senderImgView.image, diameter: 60)!
            
            self.collectionView.reloadData()
            
            self.finishReceivingMessage()
        })
        
        receiverImgView.sd_setImage(with: URL(string: CommonUtils.driver_image_url + receiverId + "/" + pPicName), placeholderImage: UIImage(named: "ic_no_pic_user"),options: SDWebImageOptions(rawValue: 0), completed: { (image, error, cacheType, imageURL) in
            
            self.receiverImg = JSQMessagesAvatarImageFactory.avatarImage(withPlaceholder: self.receiverImgView.image, diameter: 60)!
            
            self.collectionView.reloadData()
            
            self.finishSendingMessage()
        })
        
    }
    
    
    override func viewWillDisappear(_ animated: Bool) {
        GeneralFunctions.saveValue(key: "cahtViewVisible", value:false as Bool as AnyObject)
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    override func collectionView(_ collectionView: JSQMessagesCollectionView!, messageDataForItemAt indexPath: IndexPath!) -> JSQMessageData!
    {
        return messages[indexPath.item]
    }
    
    override func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int
    {
        return messages.count
    }
    
    override func collectionView(_ collectionView: JSQMessagesCollectionView!, messageBubbleImageDataForItemAt indexPath: IndexPath!) -> JSQMessageBubbleImageDataSource!
    {
        return messages[indexPath.item].senderDisplayName == Utils.appUserType ? outgoingBubble : incomingBubble
    }
    override func collectionView(_ collectionView: JSQMessagesCollectionView!, avatarImageDataForItemAt indexPath: IndexPath!) -> JSQMessageAvatarImageDataSource!
    {
        let message = messages[indexPath.row]
        
        if(message.senderDisplayName == Utils.appUserType){
            return senderImg
        }else{
            return receiverImg
        }
    }
    
    
    override func didPressSend(_ button: UIButton!, withMessageText text: String!, senderId: String!, senderDisplayName: String!, date: Date!)
    {
        let ref = Constants.refs.databaseChats.child(assignedtripId + "-Trip").childByAutoId()
        
        let msgDict = ["eUserType": Utils.appUserType, "Text": text, "iTripId": assignedtripId, "passengerImageName": userProfileJson.get("vImgName"), "driverImageName": pPicName, "passengerId": GeneralFunctions.getMemberd(), "driverId": receiverId]
        
        
        storeMessageToclientServer(textMessage: text!)
        
        ref.setValue(msgDict)
        
        finishSendingMessage()
        
    }

    func storeMessageToclientServer(textMessage:String)
    {
        
        let parameters = ["type":"SendTripMessageNotification","UserType": Utils.appUserType, "iFromMemberId": GeneralFunctions.getMemberd(), "iToMemberId": receiverId, "iTripId": assignedtripId, "tMessage": textMessage]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                
            }else{
                
            }
        })
        
    }
}
