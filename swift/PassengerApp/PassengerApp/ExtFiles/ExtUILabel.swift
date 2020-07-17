//
//  ExtUILabel.swift
//  UberCloneApp
//
//  Created by Chirag on 17/12/15.
//  Copyright © 2015 ESW. All rights reserved.
//

import UIKit

extension UILabel
{
    
    func addImage(imageName: String, afterLabel bolAfterLabel: Bool = false)
    {
        let attachment: NSTextAttachment = NSTextAttachment()
        attachment.image = UIImage(named: imageName)
        let attachmentString: NSAttributedString = NSAttributedString(attachment: attachment)
        
        if (bolAfterLabel)
        {
            let strLabelText: NSMutableAttributedString = NSMutableAttributedString(string: self.text!)
            strLabelText.append(attachmentString)
            
            self.attributedText = strLabelText
        }
        else
        {
            let strLabelText: NSAttributedString = NSAttributedString(string: self.text!)
            let mutableAttachmentString: NSMutableAttributedString = NSMutableAttributedString(attributedString: attachmentString)
            mutableAttachmentString.append(strLabelText)
            
            self.attributedText = mutableAttachmentString
        }
    }
    
    func removeImage()
    {
        let text = self.text
        self.attributedText = nil
        self.text = text
    }
    
    func setHTMLFromString(text: String) {
//        print("Hex:String:\(self.textColor.hexString)")
        
        var direction = "ltr"
        if(Configurations.isRTLMode()){
            direction = "rtl"
        }
        
        let modifiedFont = NSString(format:"<div dir=\"\(direction)\" style=\"font-family: \(self.font!.fontName); font-size: \(self.font!.pointSize); color: \(self.textColor.hexString)\">%@</div>" as NSString, text) as String
        
        let attrStr = try! NSAttributedString(
            data: modifiedFont.data(using: String.Encoding.unicode, allowLossyConversion: true)!,
            options: [NSDocumentTypeDocumentAttribute: NSHTMLTextDocumentType, NSCharacterEncodingDocumentAttribute: String.Encoding.utf8.rawValue],
            documentAttributes: nil)
        
        self.attributedText = attrStr
    }
    
    func fitText(){
        self.numberOfLines = 0
        self.sizeToFit()
    }
}
