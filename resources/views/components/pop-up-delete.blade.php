<!-- Custom Pop-up Modal -->
<div id="customModal" style="display: none; position: fixed; top: 46%; left: 50%; transform: translate(-50%, -50%);
    background: #FFE6E6; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    width: 300px; animation: zoomBounce 0.4s ease-out;">
    <h3 style="color: red; font-size: 22px; margin-bottom: 10px;">⚠️ WARNING ⚠️</h3>
    <p style="font-size: 16px;">Are you sure you want to delete this document?</p>
    <form id="deleteForm" action="{{ route('documents.destroy', $document->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" id="confirmBtn" style="background: red; color: white; padding: 10px; border: none; cursor: pointer; margin-right: 10px;
            border-radius: 5px; font-size: 14px;">Okay</button>
        <button type="button" id="cancelBtn" style="background: gray; color: white; padding: 10px; border: none; cursor: pointer; 
            border-radius: 5px; font-size: 14px;">Cancel</button>
    </form>
</div>
